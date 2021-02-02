<?php

namespace PayzeIO\LaravelPayze\Enums;

abstract class SwitchableEnum
{
    public static function check($value): bool
    {
        $supported = static::SUPPORTED ?? [];

        if (is_string($value) && (!defined(static::class . '::STRICT') || !static::STRICT)) {
            $value = strtolower($value);
            $supported = array_map(function ($item) {
                if (!is_string($item)) {
                    return $item;
                }

                return strtolower($item);
            }, $supported);
        }

        return in_array($value, $supported);
    }
}
