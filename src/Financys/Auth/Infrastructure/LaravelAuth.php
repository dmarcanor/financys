<?php

declare(strict_types=1);

namespace Financys\Auth\Infrastructure;

use DateInterval;
use DateTimeImmutable;
use Financys\Auth\Domain\Authentication;
use Financys\Auth\Domain\AuthenticationRepository;
use Illuminate\Support\Facades\Auth;

class LaravelAuth implements AuthenticationRepository
{
    public const string TOKEN_TYPE = 'bearer';

    public function authenticate(string $email, string $password): ?Authentication
    {
        $token = Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);

        if (! $token) {
            return null;
        }

        $expirationTimeInMinutes = auth()->factory()->getTTL();

        return new Authentication(
            $token,
            self::TOKEN_TYPE,
            (new DateTimeImmutable())->add(new DateInterval("PT{$expirationTimeInMinutes}M"))
        );
    }
}
