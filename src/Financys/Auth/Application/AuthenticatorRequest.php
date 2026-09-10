<?php

declare(strict_types=1);

namespace Financys\Auth\Application;

class AuthenticatorRequest
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {}
}