<?php

declare(strict_types = 1);

namespace Tests\Unit\Src\Account\Application\Create;

use Financys\Account\Application\Creator\AccountCreatorRequest;

final class AccountCreatorRequestMother
{
    public static function create(
        ?string $id = null,
        ?string $userId = null,
        ?string $code = null,
        ?string $name = null,
        ?string $balance = null,
        ?string $currency = null
    ): AccountCreatorRequest
    {
        return new AccountCreatorRequest(
            $id ?? fake()->uuid,
            $userId ?? fake()->uuid(),
            $code ?? fake()->word(),
            $name ?? fake()->name(),
            $balance ?? (string) number_format(fake()->randomFloat(nbMaxDecimals: 8), 8, '.', ''),
            $currency ?? fake()->randomElement(['bs', 'usd']),
        );
    }
}