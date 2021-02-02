<?php

namespace PayzeIO\LaravelPayze\Requests;

use PayzeIO\LaravelPayze\Concerns\PayRequest;
use PayzeIO\LaravelPayze\Enums\Method;

class JustPay extends PayRequest
{
    /**
     * @var string
     */
    protected $method = Method::JUST_PAY;

    /**
     * JustPay constructor.
     *
     * @param float $amount
     */
    public function __construct(float $amount)
    {
        $this->amount($amount);
    }
}
