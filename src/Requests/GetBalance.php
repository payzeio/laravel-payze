<?php

namespace PayzeIO\LaravelPayze\Requests;

use PayzeIO\LaravelPayze\Concerns\ApiRequest;
use PayzeIO\LaravelPayze\Enums\Method;

class GetBalance extends ApiRequest
{
    /**
     * @var string
     */
    protected string $method = Method::BALANCE;
}
