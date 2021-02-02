<?php

namespace PayzeIO\LaravelPayze\Exceptions;

use Exception;

class UnsupportedCurrencyException extends Exception
{
    /**
     * UnsupportedLanguageException constructor.
     *
     * @param string $currency
     */
    public function __construct(string $currency)
    {
        parent::__construct();

        $this->message = sprintf('Unsupported Currency "%s"', $currency);
    }
}
