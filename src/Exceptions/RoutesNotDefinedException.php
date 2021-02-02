<?php

namespace PayzeIO\LaravelPayze\Exceptions;

use Exception;

class RoutesNotDefinedException extends Exception
{
    protected $message = 'Payze routes are not defined';
}
