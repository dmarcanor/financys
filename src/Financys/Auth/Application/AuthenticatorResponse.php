<?php

declare(strict_types=1);

namespace Financys\Auth\Application;

use DateTimeImmutable;

class AuthenticatorResponse
{
    public function __construct(
        public readonly string $userId,
        public readonly string $token,
        public readonly string $type,
        public readonly string $expiresAt
    ) {}
}