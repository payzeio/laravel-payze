<?php

namespace PayzeIO\LaravelPayze\Exceptions;

use Exception;

class CardTokenInactiveException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Card token is inactive';
}
