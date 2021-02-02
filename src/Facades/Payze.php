<?php

namespace PayzeIO\LaravelPayze\Facades;

use Illuminate\Support\Facades\Facade;

class Payze extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \PayzeIO\LaravelPayze\Payze::class;
    }
}
