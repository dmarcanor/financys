<?php

declare(strict_types=1);

namespace Financys\Auth\Infrastructure;

use Auth;
use Financys\Auth\Domain\Authentication;
use Financys\Auth\Domain\AuthenticationRepository;

class LaravelAuth implements AuthenticationRepository
{
    public function authenticate(string $email, string $password): ?Authentication
    {
        $token = Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);

        if (! $token) {
            return null;
        }

        return new Authentication;
    }
}
