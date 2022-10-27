<?php

namespace PayzeIO\LaravelPayze\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;
use PayzeIO\LaravelPayze\Requests\GetTransactionInfo;

class PayzeController extends Controller
{
    /**
     * Query string key of transaction ID
     *
     * @var string
     */
    protected string $key = 'payment_transaction_id';

    /**
     * Successful payment's view
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function success(Request $request)
    {
        $transaction = $this->getTransaction($request);

        if (!$transaction->is_paid) {
            return redirect()->route(config('payze.routes.fail'), $request->query());
        }

        $response = $this->successResponse($transaction, $request);

        return $response ?? $this->response('success');
    }

    /**
     * Failed payment view
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function fail(Request $request)
    {
        $transaction = $this->getTransaction($request);

        if ($transaction->is_paid) {
            return redirect()->route(config('payze.routes.success'), $request->query());
        }

        $response = $this->failResponse($transaction, $request);

        return $response ?? $this->response('fail');
    }

    /**
     * Update information in database and return transaction
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeTransaction
     */
    protected function getTransaction(Request $request): PayzeTransaction
    {
        abort_unless($request->has($this->key), 404);

        $id = $request->input($this->key);

        /*
         * Check if transaction is incomplete
         * Fixes security issue. Avoids triggering success callbacks on completed transactions more than once
         */
        PayzeTransaction::where('transaction_id', $id)->incomplete()->firstOrFail();

        return GetTransactionInfo::request($id)->process();
    }

    /**
     * Return a view or redirect to index page, based on config
     *
     * @param string $status
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    protected function response(string $status)
    {
        $view = config('payze.views.' . $status);

        return $view ? view($view) : redirect('/');
    }

    /**
     * Success Response
     * Should be overridden in custom controller, or will be used a default one
     *
     * @param \PayzeIO\LaravelPayze\Models\PayzeTransaction $transaction
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    protected function successResponse(PayzeTransaction $transaction, Request $request)
    {
        // Override in controller
    }

    /**
     * Fail Response
     * Should be overridden in custom controller, or will be used a default one
     *
     * @param \PayzeIO\LaravelPayze\Models\PayzeTransaction $transaction
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    protected function failResponse(PayzeTransaction $transaction, Request $request)
    {
        // Override in controller
    }
}
