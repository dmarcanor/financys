<?php

declare(strict_types=1);

namespace Financys\Auth\Domain;

interface AuthenticationRepository
{
    public function authenticate(string $email, string $password): ?Authentication;
}
