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

    public function isNegative(): bool
    {
        return $this->amount[0] === '-';
    }

    private function normalizeAmount(string $amount): string
    {
        return bcadd('0', $amount, self::DECIMAL_PLACES);
    }

    public function symbol(): string
    {
        return $this->symbol->value;
    }

    public function amount(): string
    {
        return $this->amount;
    }
}