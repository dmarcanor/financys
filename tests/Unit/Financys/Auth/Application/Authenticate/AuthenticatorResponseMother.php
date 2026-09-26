<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Auth\Application\Authenticate;

use Financys\Auth\Application\Authenticate\AuthenticatorResponse;

final class AuthenticatorResponseMother
{
    public static function create(
        ?string $token = null,
        ?string $type = null,
        ?string $expiresAt = null
    ): AuthenticatorResponse {
        return new AuthenticatorResponse(
            $token ?? fake()->uuid(),
            $type ?? fake()->randomElement(['bearer', 'jwt']),
            $expiresAt ?? fake()->dateTimeBetween('now', '+1 hour')->format('Y-m-d H:i:s')
        );
    }
}
