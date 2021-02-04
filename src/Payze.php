<?php

namespace PayzeIO\LaravelPayze;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use PayzeIO\LaravelPayze\Concerns\ApiRequest;
use PayzeIO\LaravelPayze\Concerns\PayRequest;
use PayzeIO\LaravelPayze\Concerns\PayRequestAttributes;
use PayzeIO\LaravelPayze\Controllers\PayzeController;
use PayzeIO\LaravelPayze\Enums\Language;
use PayzeIO\LaravelPayze\Enums\Method;
use PayzeIO\LaravelPayze\Enums\Status;
use PayzeIO\LaravelPayze\Exceptions\ApiCredentialsException;
use PayzeIO\LaravelPayze\Exceptions\PaymentRequestException;
use PayzeIO\LaravelPayze\Models\PayzeCardToken;
use PayzeIO\LaravelPayze\Models\PayzeLog;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;
use stdClass;

class Payze
{
    /**
     * Process pay request
     *
     * @param \PayzeIO\LaravelPayze\Concerns\PayRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \PayzeIO\LaravelPayze\Exceptions\PaymentRequestException
     * @throws \PayzeIO\LaravelPayze\Exceptions\ApiCredentialsException
     * @throws \Throwable
     */
    public function processPayment(PayRequest $request): RedirectResponse
    {
        $method = $request->getMethod();
        $data = $request->toRequest();

        $this->log("Starting [$method] payment.", compact('method', 'data'));
        $response = $this->request($method, $data)['response'];

        $url = $response['transactionUrl'];
        $id = $response['transactionId'];

        throw_if(empty($id) || empty($url), new PaymentRequestException('Transaction ID is missing'));

        $this->log(
            "Transaction [$id] created",
            compact('id', 'response'),
            $transaction = $this->logTransaction($response ?? [], $request)
        );

        if ($method === Method::ADD_CARD && filled($response['cardId'] ?? false)) {
            $this->saveCard($response['cardId'], $transaction);
        }

        return redirect($url);
    }

    /**
     * @param \PayzeIO\LaravelPayze\Concerns\ApiRequest $request
     * @param string|null $key
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeTransaction
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PayzeIO\LaravelPayze\Exceptions\ApiCredentialsException
     * @throws \PayzeIO\LaravelPayze\Exceptions\PaymentRequestException
     * @throws \Throwable
     */
    public function processTransaction(ApiRequest $request, string $key = null): PayzeTransaction
    {
        $response = $this->process($request);

        if ($key) {
            $response = $response[$key];
        }

        return Payze::logTransaction($response, is_a($request, PayRequestAttributes::class) ? $request : null);
    }

    /**
     * @param \PayzeIO\LaravelPayze\Concerns\ApiRequest $request
     * @param bool $raw
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PayzeIO\LaravelPayze\Exceptions\ApiCredentialsException
     * @throws \PayzeIO\LaravelPayze\Exceptions\PaymentRequestException
     * @throws \Throwable
     */
    public function process(ApiRequest $request, bool $raw = false): array
    {
        $method = $request->getMethod();
        $data = $request->toRequest();

        $this->log("Sending [$method] request", $data);

        $response = $this->request($method, $data);

        $this->log("Received [$method] response", $response);

        return $raw ? $response : $response['response'];
    }

    /**
     * @param string $token
     * @param \PayzeIO\LaravelPayze\Models\PayzeTransaction $transaction
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeCardToken
     */
    protected function saveCard(string $token, PayzeTransaction $transaction): PayzeCardToken
    {
        return PayzeCardToken::create([
            'token' => $token,
            'transaction_id' => $transaction->id,
            'model_id' => $transaction->model_id,
            'model_type' => $transaction->model_type,
        ]);
    }

    /**
     * Create/update transaction entry in database
     *
     * @param array $data
     * @param \PayzeIO\LaravelPayze\Concerns\PayRequestAttributes|null $request
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeTransaction
     */
    public function logTransaction(array $data, PayRequestAttributes $request = null): PayzeTransaction
    {
        return tap(PayzeTransaction::firstOrNew([
            'transaction_id' => $data['transactionId'],
        ]), function (PayzeTransaction $transaction) use ($data, $request) {
            $transaction->fill(array_merge($request ? $request->toModel() : [], [
                'split' => $data['split'] ?? null,
                'status' => $status = $data['status'] ?? Status::CREATED,
                'is_paid' => Status::isPaid($status),
                'is_completed' => Status::isCompleted($status),
                'commission' => $data['commission'] ?? null,
                'final_amount' => $data['finalAmount'] ?? null,
                'can_be_committed' => $data['getCanBeCommitted'] ?? false,
                'refunded' => $data['refunded'] ?? null,
                'refundable' => $data['refundable'] ?? false,
                'card_mask' => $data['cardMask'] ?? null,
                'result_code' => $data['resultCode'] ?? null,
                'log' => $data['log'] ?? null,
            ]));

            foreach (['amount', 'currency'] as $field) {
                if (empty($transaction->$field) && !empty($data[$field])) {
                    $transaction->$field = $data[$field];
                }
            }

            if (empty($transaction->lang)) {
                $transaction->lang = Language::DEFAULT;
            }

            $transaction->save();

            if ($transaction->is_paid) {
                $transaction->cards()->where('active', false)->update(['active' => true]);
            }
        });
    }

    /**
     * Create log entry in database
     *
     * @param string $message
     * @param array $data
     * @param \PayzeIO\LaravelPayze\Models\PayzeTransaction|null $transaction
     */
    public function log(string $message, array $data = [], PayzeTransaction $transaction = null): void
    {
        if (!config('payze.log')) {
            return;
        }

        PayzeLog::create([
            'transaction_id' => optional($transaction)->id,
            'message' => $message,
            'payload' => $data,
        ]);
    }

    /**
     * Send API request
     *
     * @param string $method
     * @param array $data
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PayzeIO\LaravelPayze\Exceptions\PaymentRequestException
     * @throws \PayzeIO\LaravelPayze\Exceptions\ApiCredentialsException
     * @throws \Throwable
     */
    public function request(string $method, array $data = []): array
    {
        $key = config('payze.api_key');
        $secret = config('payze.api_secret');

        throw_if(empty($key) || empty($secret), new ApiCredentialsException);

        try {
            $response = json_decode((new Client)->post('https://payze.io/api/v1', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'user-agent' => 'laravel-payze',
                ],
                'json' => [
                    'method' => $method,
                    'apiKey' => $key,
                    'apiSecret' => $secret,
                    'data' => $data ?: new stdClass,
                ],
            ])->getBody()->getContents(), true);
        } catch (RequestException $exception) {
            throw new PaymentRequestException($exception->getMessage());
        }

        throw_unless(empty($response['response']['error']), new PaymentRequestException($response['response']['error'] ?? 'Error'));

        return $response;
    }

    /**
     * @param $transaction
     *
     * @return string
     * @throws \PayzeIO\LaravelPayze\Exceptions\PaymentRequestException
     * @throws \Throwable
     */
    public static function parseTransaction($transaction): string
    {
        $isString = is_string($transaction) && filled($transaction);
        $isTransaction = is_a($transaction, PayzeTransaction::class);

        throw_unless($isString || $isTransaction, new PaymentRequestException('Please specify valid transaction'));

        if ($isTransaction) {
            return $transaction->transaction_id;
        }

        return $transaction;
    }

    /**
     * Define success and fail routes
     *
     * @return void
     */
    public static function routes(): void
    {
        Route::prefix('payze')->name('payze.')->group(function () {
            Route::get('success', [PayzeController::class, 'success'])->name('success');
            Route::get('fail', [PayzeController::class, 'fail'])->name('fail');
        });
    }
}
