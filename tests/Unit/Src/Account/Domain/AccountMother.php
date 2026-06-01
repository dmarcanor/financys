<?php

declare(strict_types = 1);

namespace Tests\Unit\Src\Account\Domain;

use Financys\Account\Domain\Account;

class AccountMother
{
    public static function create(
        ?string $id = null,
        ?string $userId = null,
        ?string $name = null,
        ?float $balance = null,
        ?string $currency = null
    ): Account
    {
        $faker = \Faker\Factory::create();

        return new Account(
            $id ?? $faker->uuid,
            $userId ?? $faker->uuid(),
            $name ?? $faker->name(),
            $balance ?? $faker->randomFloat(),
            $currency ?? $faker->currencyCode(),
        );
    }
}