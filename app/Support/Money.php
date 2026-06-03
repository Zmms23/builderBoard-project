<?php

namespace App\Support;

class Money
{
    public static function toAmount(int | float | string | null $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) round(((float) str_replace(',', '.', (string) $value)) * 100);
    }

    public static function fromAmount(int | float | string | null $amount): string
    {
        return number_format(((int) ($amount ?: 0)) / 100, 2, '.', '');
    }

    public static function format(int | float | string | null $amount, string $currency): string
    {
        return self::fromAmount($amount) . ' ' . $currency;
    }
}
