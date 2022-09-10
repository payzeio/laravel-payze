<?php

namespace PayzeIO\LaravelPayze\Enums;

class Currency extends SwitchableEnum
{
    const DEFAULT = self::GEL;

    const STRICT = true;

    const SUPPORTED = [
        self::GEL,
        self::USD,
        self::UZS,
    ];

    const GEL = 'GEL';

    const USD = 'USD';
    
    const UZS = 'UZS';
}
