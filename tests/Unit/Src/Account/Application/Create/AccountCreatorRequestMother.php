<?php

declare(strict_types = 1);

namespace Tests\Unit\Src\Account\Application\Create;

use Financys\Account\Application\Creator\AccountCreatorRequest;

final class AccountCreatorRequestMother
{
    public static function create(
        ?string $id = null,
        ?string $userId = null,
        ?string $name = null,
        ?float $balance = null,
        ?string $currency = null
    ): AccountCreatorRequest
    {
        $faker = \Faker\Factory::create();

        return new AccountCreatorRequest(
            $id ?? $faker->uuid,
            $userId ?? $faker->uuid(),
            $name ?? $faker->name(),
            $balance ?? $faker->randomFloat(),
            $currency ?? $faker->currencyCode(),
        );
    }
}