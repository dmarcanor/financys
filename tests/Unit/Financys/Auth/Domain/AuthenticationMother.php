<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Auth\Domain;

use DateTimeImmutable;
use Financys\Auth\Domain\Authentication;

class AuthenticationMother
{
    public static function create(
        ?string $token = null,
        ?string $type = null,
        ?DateTimeImmutable $expiresAt = null
    ): Authentication {
        return new Authentication(
            $token ?? fake()->uuid(),
            $type ?? fake()->randomElement(['bearer', 'jwt']),
            $expiresAt ?? new DateTimeImmutable(fake()->dateTimeBetween('now', '+1 hour')->format('Y-m-d H:i:s'))
        );
    }
}
