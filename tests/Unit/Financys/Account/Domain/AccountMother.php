<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Account\Domain;

use Financys\Account\Domain\Account;
use Financys\Account\Domain\AccountCode;
use Financys\Account\Domain\AccountName;
use Shared\Domain\Symbols;
use Shared\Domain\Uuid;

class AccountMother
{
    public static function create(
        ?string $id = null,
        ?string $userId = null,
        ?string $code = null,
        ?string $name = null,
        ?string $currency = null
    ): Account {
        return Account::create(
            new Uuid($id ?? fake()->uuid),
            new Uuid($userId ?? fake()->uuid()),
            new AccountCode($code ?? fake()->word()),
            new AccountName($name ?? fake()->name()),
            Symbols::from($currency ?? fake()->randomElement(['bs', 'usd'])),
        );
    }
}
