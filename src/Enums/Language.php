<?php

namespace PayzeIO\LaravelPayze\Enums;

class Language extends SwitchableEnum
{
    const DEFAULT = self::GEO;

    const SUPPORTED = [
        self::GEO,
        self::ENG,
        self::UZB,
    ];

    const ENG = 'en';

    const GEO = 'ge';
    
    const UZB = 'uz';
}
