<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Auth\Application\Authenticate;

use Financys\Auth\Application\Authenticate\AuthenticatorRequest;

final class AuthenticatorRequestMother
{
    public static function create(
        ?string $email = null,
        ?string $password = null
    ): AuthenticatorRequest {
        return new AuthenticatorRequest(
            $email ?? fake()->email(),
            $password ?? fake()->password()
        );
    }
}
