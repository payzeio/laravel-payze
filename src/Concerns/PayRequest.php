<?php

namespace PayzeIO\LaravelPayze\Concerns;

use PayzeIO\LaravelPayze\Facades\Payze;

abstract class PayRequest extends PayRequestAttributes
{
    /**
     * Process payment via Payze facade
     *
     * @return mixed
     */
    public function process()
    {
        return Payze::processPayment($this);
    }
}
