<?php

namespace PayzeIO\LaravelPayze\Requests;

use PayzeIO\LaravelPayze\Concerns\ApiRequest;
use PayzeIO\LaravelPayze\Enums\Method;
use PayzeIO\LaravelPayze\Facades\Payze;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;

class Refund extends ApiRequest
{
    /**
     * @var string
     */
    protected string $method = Method::REFUND;

    /**
     * @var string
     */
    protected string $transactionId;

    /**
     * @var float
     */
    protected float $amount = 0;

    /**
     * Refund constructor.
     *
     * @param $transaction
     * @param float|null $amount
     */
    public function __construct($transaction, float $amount = 0)
    {
        $this->transactionId = Payze::parseTransaction($transaction);
        $this->amount = $amount;
    }

    /**
     * Process request via Payze facade
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeTransaction
     */
    public function process(): PayzeTransaction
    {
        return Payze::processTransaction($this, 'transaction');
    }

    /**
     * @return array
     */
    public function toRequest(): array
    {
        return array_filter([
            'transactionId' => $this->transactionId,
            'amount' => $this->amount ?: null,
        ]);
    }
}
