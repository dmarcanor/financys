<?php

declare(strict_types=1);

namespace Financys\Account\Domain;

use Shared\Domain\Currency;
use Shared\Domain\NegativeCurrency;
use Shared\Domain\Symbols;

final class AccountBalance extends Currency
{
    public static function create(Symbols $symbol, string $amount): self
    {
        $accountBalance = new self($symbol, $amount);

        if ($accountBalance->isNegative()) {
            throw new NegativeCurrency(sprintf("The account balance %f %s can't be negative", $amount, $symbol->value));
        }

        return $accountBalance;
    }
}
