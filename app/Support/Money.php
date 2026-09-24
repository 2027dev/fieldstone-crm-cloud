<?php

namespace App\Support;

class Money
{
    public static function format(float|string|null $amount, string $currency = 'USD'): string
    {
        $symbol = match ($currency) {
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };

        return $symbol.number_format((float) $amount, 0);
    }

    public static function compact(float $amount): string
    {
        return match (true) {
            $amount >= 1_000_000 => '$'.round($amount / 1_000_000, 1).'M',
            $amount >= 1_000 => '$'.round($amount / 1_000, 1).'k',
            default => '$'.number_format($amount),
        };
    }
}
