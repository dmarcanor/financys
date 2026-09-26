<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Account\Application\Create;

use Financys\Account\Application\Create\AccountCreatorRequest;

final class AccountCreatorRequestMother
{
    public static function create(
        ?string $id = null,
        ?string $userId = null,
        ?string $code = null,
        ?string $name = null,
        ?string $currency = null
    ): AccountCreatorRequest {
        return new AccountCreatorRequest(
            $id ?? fake()->uuid,
            $userId ?? fake()->uuid(),
            $code ?? fake()->word(),
            $name ?? fake()->name(),
            $currency ?? fake()->randomElement(['bs', 'usd']),
        );
    }
}
