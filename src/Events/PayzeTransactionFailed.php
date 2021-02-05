<?php

namespace PayzeIO\LaravelPayze\Events;

use Illuminate\Queue\SerializesModels;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;

class PayzeTransactionFailed
{
    use SerializesModels;

    /**
     * @var \PayzeIO\LaravelPayze\Models\PayzeTransaction
     */
    public $transaction;

    /**
     * PayzeTransactionPaid constructor.
     *
     * @param \PayzeIO\LaravelPayze\Models\PayzeTransaction $transaction
     */
    public function __construct(PayzeTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
