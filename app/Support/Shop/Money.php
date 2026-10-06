<?php

namespace App\Support\Shop;

/**
 * Currency formatting. IDR by default; other currencies are supported by
 * the registry below (symbol, decimals, separators).
 */
class Money
{
    public const CURRENCIES = [
        'IDR' => ['Rp ', 0, ',', '.'],
        'USD' => ['$', 2, '.', ','],
        'SGD' => ['S$', 2, '.', ','],
        'MYR' => ['RM ', 2, '.', ','],
        'EUR' => ['€', 2, ',', '.'],
    ];

    private static string $currency = 'IDR';

    public static function setCurrency(string $currency): void
    {
        self::$currency = isset(self::CURRENCIES[$currency]) ? $currency : 'IDR';
    }

    public static function currency(): string
    {
        return self::$currency;
    }

    public static function format(float|int|string|null $amount, ?string $currency = null): string
    {
        [$symbol, $decimals, $decimalSep, $thousandsSep] = self::CURRENCIES[$currency ?? self::$currency] ?? self::CURRENCIES['IDR'];

        return $symbol.number_format((float) $amount, $decimals, $decimalSep, $thousandsSep);
    }

    /** Round to the currency's precision. */
    public static function round(float $amount, ?string $currency = null): float
    {
        return round($amount, self::CURRENCIES[$currency ?? self::$currency][1] ?? 0);
    }
}
