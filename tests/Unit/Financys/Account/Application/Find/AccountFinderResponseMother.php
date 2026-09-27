<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Account\Application\Find;

use Financys\Account\Application\Find\AccountFinderResponse;

final class AccountFinderResponseMother
{
    public static function create(
        ?string $id = null,
        ?string $userId = null,
        ?string $code = null,
        ?string $name = null,
        ?string $balance = null,
        ?string $currency = null
    ): AccountFinderResponse {
        return new AccountFinderResponse(
            $id ?? fake()->uuid,
            $userId ?? fake()->uuid,
            $code ?? fake()->word,
            $name ?? fake()->name,
            $balance ?? fake()->randomNumber(4) . '.' . str_pad((string) fake()->randomNumber(2), 2, '0', STR_PAD_LEFT),
            $currency ?? fake()->randomElement(['bs', 'usd'])
        );
    }
}
