<?php

declare(strict_types=1);

namespace Financys\Auth\Domain;

use DateTimeImmutable;

class Authentication
{
    public function __construct(
        public readonly string $token,
        public readonly string $type,
        public readonly DateTimeImmutable $expiresAt
    ) {}
}
