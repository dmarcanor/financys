<?php

declare(strict_types = 1);

namespace Shared\Domain;

class Currency
{
    private const DECIMAL_PLACES = 8;

    public function __construct(
        protected Symbols $symbol, 
        protected string $amount
    ) {
        $this->amount = $this->normalizeAmount($amount);
    }

    protected function isNegative(): bool
    {
        return $this->leftOfDecimal($this->amount) !== '0' && $this->amount[0] === '-';
    }

    private function normalizeAmount(string $amount): string
    {
        $negative = str_starts_with($amount, '-');

        [$integer, $fraction] = array_pad(explode('.', ltrim($amount, '-+')), 2, '');

        $integer = ltrim($integer, '0');

        return ($negative ? '-' : '')
            . ($integer === '' ? '0' : $integer)
            . '.'
            . str_pad(substr($fraction, 0, self::DECIMAL_PLACES), self::DECIMAL_PLACES, '0');
    }

    private function leftOfDecimal(string $amount): string
    {
        return explode('.', ltrim($amount, '-+'))[0];
    }
}