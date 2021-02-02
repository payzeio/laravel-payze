<?php

namespace PayzeIO\LaravelPayze\Requests;

use PayzeIO\LaravelPayze\Concerns\ApiRequest;
use PayzeIO\LaravelPayze\Enums\Method;
use PayzeIO\LaravelPayze\Facades\Payze;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;

class GetTransactionInfo extends ApiRequest
{
    /**
     * @var string
     */
    protected $method = Method::TRANSACTION_INFO;

    /**
     * @var string
     */
    protected $transactionId;

    /**
     * GetTransactionInfo constructor.
     *
     * @param $transaction
     */
    public function __construct($transaction)
    {
        $this->transactionId = Payze::parseTransaction($transaction);
    }

    /**
     * Process request via Payze facade
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeTransaction
     */
    public function process(): PayzeTransaction
    {
        return Payze::processTransaction($this);
    }

    /**
     * @return array
     */
    public function toRequest(): array
    {
        return [
            'transactionId' => $this->transactionId,
        ];
    }
}
