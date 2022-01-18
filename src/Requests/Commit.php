<?php

namespace PayzeIO\LaravelPayze\Requests;

use PayzeIO\LaravelPayze\Concerns\ApiRequest;
use PayzeIO\LaravelPayze\Enums\Method;
use PayzeIO\LaravelPayze\Facades\Payze;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;

class Commit extends ApiRequest
{
    /**
     * @var string
     */
    protected $method = Method::COMMIT;

    /**
     * @var float
     */
    protected $amount = 0;

    /**
     * @var string
     */
    protected $transactionId;

    /**
     * Commit constructor.
     *
     * @param $transaction
     * @param float|int $amount
     */
    public function __construct($transaction, float $amount = 0)
    {
        $this->transactionId = Payze::parseTransaction($transaction);
        $this->amount($amount);
    }

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
     * Process request via Payze facade
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeTransaction
     */
    public function process(): PayzeTransaction
    {
        return Payze::processTransaction($this, 'data');
    }

    public function toRequest(): array
    {
        return array_filter([
            'transactionId' => $this->transactionId,
            'amount' => $this->amount ?: null,
        ]);
    }
}
