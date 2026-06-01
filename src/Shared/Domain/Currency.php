<?php

declare(strict_types = 1);

namespace Shared\Domain;

class Currency
{
    public function __construct(
        protected Symbols $symbol, 
        protected float $amount
    ) {}

    protected function isNegative(): bool
    {
        return $this->amount < 0;
    }
}