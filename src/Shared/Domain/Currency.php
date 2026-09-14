<?php

declare(strict_types = 1);

namespace Shared\Domain;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

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
        return BigDecimal::of($amount)->toScale(self::DECIMAL_PLACES, RoundingMode::Down)->__toString();
    }

    private function leftOfDecimal(string $amount): string
    {
        return explode('.', ltrim($amount, '-+'))[0];
    }
}