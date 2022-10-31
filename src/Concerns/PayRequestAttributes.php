<?php

namespace PayzeIO\LaravelPayze\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use PayzeIO\LaravelPayze\Enums\Currency;
use PayzeIO\LaravelPayze\Enums\Language;
use PayzeIO\LaravelPayze\Facades\Payze;
use PayzeIO\LaravelPayze\Exceptions\PaymentRequestException;
use PayzeIO\LaravelPayze\Exceptions\RoutesNotDefinedException;
use PayzeIO\LaravelPayze\Exceptions\UnsupportedCurrencyException;
use PayzeIO\LaravelPayze\Exceptions\UnsupportedLanguageException;
use PayzeIO\LaravelPayze\Objects\Split;

abstract class PayRequestAttributes extends ApiRequest
{
    /**
     * @var float
     */
    protected float $amount = 0;

    /**
     * @var array
     */
    protected array $split = [];

    /**
     * @var string
     */
    protected string $currency = Currency::DEFAULT;

    /**
     * @var string
     */
    protected string $lang = Language::DEFAULT;

    /**
     * @var bool
     */
    protected bool $preauthorize = false;

    /**
     * @var bool
     */
    protected bool $raw = false;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $model;

    /**
     * @var string
     */
    protected string $callback = '';

    /**
     * @var string
     */
    protected string $callbackError = '';

    /**
     * @param float $amount
     *
     * @return $this
     */
    public function amount(float $amount): self
    {
        $this->amount = max($amount, 0);

        return $this;
    }

    /**
     * Split transaction to different IBANs
     *
     * @param mixed $splits
     *
     * @return $this
     * @throws \PayzeIO\LaravelPayze\Exceptions\PaymentRequestException
     * @throws \Throwable
     */
    public function split($splits): self
    {
        $splits = !is_array($splits) ? func_get_args() : $splits;

        foreach ($splits as $split) {
            throw_unless(is_a($split, Split::class), new PaymentRequestException('Incorrect format. Please pass Split object'));
        }

        $this->split = $splits;

        return $this;
    }

    /**
     * Switch payment page language
     *
     * @param string $lang
     *
     * @return $this
     * @throws \PayzeIO\LaravelPayze\Exceptions\UnsupportedLanguageException|\Throwable
     */
    public function language(string $lang): self
    {
        throw_unless(Language::check($lang), new UnsupportedLanguageException($lang));

        $this->lang = $lang;

        return $this;
    }

    /**
     * Switch payment page language
     *
     * @param string $currency
     *
     * @return $this
     * @throws \PayzeIO\LaravelPayze\Exceptions\UnsupportedCurrencyException|\Throwable
     */
    public function currency(string $currency): self
    {
        $currency = strtoupper($currency);

        throw_unless(Currency::check($currency), new UnsupportedCurrencyException($currency));

        $this->currency = $currency;

        return $this;
    }

    /**
     * Set the model for transaction
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return $this
     */
    public function for(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set preauthorize option for transaction
     *
     * @param bool $preauthorize
     *
     * @return $this
     */
    public function preauthorize(bool $preauthorize = true): self
    {
        $this->preauthorize = $preauthorize;

        return $this;
    }

    /**
     * Set raw option for transaction
     *
     * @param bool $raw
     *
     * @return $this
     */
    public function raw(bool $raw = true): self
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * The user will be redirected to this URL, If the transaction is successful
     *
     * @param string $url
     *
     * @return $this
     */
    public function callback(string $url): self
    {
        $this->callback = $url;

        return $this;
    }

    /**
     * The user will be redirected to this URL, if the transaction fails
     *
     * @param string $url
     *
     * @return $this
     */
    public function callbackError(string $url): self
    {
        $this->callbackError = $url;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
     * @return bool
     */
    public function getPreauthorize(): bool
    {
        return $this->preauthorize;
    }

    /**
     * @return bool
     */
    public function getRaw(): bool
    {
        return $this->raw;
    }

    /**
     * @return array
     */
    public function getSplit(): array
    {
        return $this->split;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * @return array
     * @throws \PayzeIO\LaravelPayze\Exceptions\RoutesNotDefinedException
     * @throws \Throwable
     */
    public function toRequest(): array
    {
        $defaultRoutes = config('payze.routes');

        if ($this->callback) {
            $successName = $this->callback;
        } else {
            throw_if(empty($defaultRoutes['success'] ?? false) || !Route::has($defaultRoutes['success']), new RoutesNotDefinedException);

            $successName = route($defaultRoutes['success']);
        }

        if ($this->callbackError) {
            $failName = $this->callbackError;
        } else {
            throw_if(empty($defaultRoutes['fail'] ?? false) || !Route::has($defaultRoutes['fail']), new RoutesNotDefinedException);

            $failName = route($defaultRoutes['fail']);
        }

        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'lang' => $this->lang,
            'preauthorize' => $this->preauthorize,
            'callback' => $successName,
            'callbackError' => $failName,
        ];
    }

    /**
     * @return array
     */
    public function toModel(): array
    {
        $data = [
            'model_id' => optional($this->model)->id,
            'model_type' => $this->model ? Payze::modelType($this->model) : null,
            'method' => $this->method,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'lang' => $this->lang,
        ];

        if (!empty($this->split)) {
            $data['split'] = $this->split;
        }

        return $data;
    }
}
