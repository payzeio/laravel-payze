<?php

namespace PayzeIO\LaravelPayze\Observers;

use PayzeIO\LaravelPayze\Events\PayzeTransactionFailed;
use PayzeIO\LaravelPayze\Events\PayzeTransactionPaid;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;

class PayzeTransactionObserver
{
    /**
     * @param \PayzeIO\LaravelPayze\Models\PayzeTransaction $transaction
     */
    public function saved(PayzeTransaction $transaction)
    {
        $completed = $transaction->is_completed && !$transaction->getOriginal('is_completed');

        if ($completed && ($transaction->is_paid && !$transaction->getOriginal('is_paid'))) {
            event(new PayzeTransactionPaid($transaction));
        } elseif ($completed && (!$transaction->is_paid && !$transaction->getOriginal('is_paid'))) {
            event(new PayzeTransactionFailed($transaction));
        }
    }
}
