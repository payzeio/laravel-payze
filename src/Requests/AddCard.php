<?php

namespace PayzeIO\LaravelPayze\Requests;

use PayzeIO\LaravelPayze\Concerns\PayRequest;
use PayzeIO\LaravelPayze\Enums\Method;

class AddCard extends PayRequest
{
    /**
     * @var string
     */
    protected $method = Method::ADD_CARD;

    /**
     * AddCard constructor.
     *
     * @param float $amount
     */
    public function __construct(float $amount = 0)
    {
        $this->amount($amount);
    }
}
