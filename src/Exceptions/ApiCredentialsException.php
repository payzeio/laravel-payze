<?php

namespace PayzeIO\LaravelPayze\Exceptions;

use Exception;

class ApiCredentialsException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Please specify Payze API credentials.';
}
