<?php

declare(strict_types = 1);

namespace Financys\Account\Domain;

use Shared\Domain\Currency;
use Shared\Domain\NegativeCurrency;
use Shared\Domain\Symbols;

final class AccountBalance extends Currency
{
    public function __construct(
        protected Symbols $symbol,
        protected float $amount
    ) {
        parent::__construct($symbol, $amount);
        
        if ($this->isNegative()) {
            throw new NegativeCurrency(sprintf("The account balance %f %s can't be negative ", $amount, $symbol));
        }
    }

    public function symbol(): string
    {
        return $this->symbol->value;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}