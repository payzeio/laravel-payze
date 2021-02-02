<?php

namespace PayzeIO\LaravelPayze\Concerns;

use PayzeIO\LaravelPayze\Facades\Payze;

abstract class ApiRequest
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function toRequest(): array
    {
        return [];
    }

    /**
     * Return new instance
     *
     * @return static
     */
    public static function request(): self
    {
        return new static(...func_get_args());
    }

    /**
     * Process request via Payze facade
     *
     * @return mixed
     */
    public function process()
    {
        return Payze::process($this);
    }
}
