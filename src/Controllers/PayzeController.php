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
    protected $key = 'payment_transaction_id';

    /**
     * Successful payment's view
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        $transaction = $this->getTransaction($request);

        if (!$transaction->is_paid) {
            return redirect()->route(config('payze.routes.fail'), $request->query());
        }

        return $this->response('success');
    }

    /**
     * Failed payment view
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function fail(Request $request)
    {
        $transaction = $this->getTransaction($request);

        if ($transaction->is_paid) {
            return redirect()->route(config('payze.routes.success'), $request->query());
        }

        return $this->response('fail');
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

        return GetTransactionInfo::request($request->input($this->key))->process();
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
}
