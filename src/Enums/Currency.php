<?php

namespace PayzeIO\LaravelPayze\Enums;

class Currency extends SwitchableEnum
{
    const DEFAULT = self::GEL;

    const STRICT = true;

    const SUPPORTED = [
        self::GEL,
        self::USD,
    ];

    const GEL = 'GEL';

    const USD = 'USD';
}
