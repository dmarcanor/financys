<?php

declare(strict_types=1);

namespace Financys\Auth\Domain;

use DateTimeImmutable;
use Shared\Domain\Uuid;

class Authentication
{
    public function __construct(
        public readonly string $userId,
        public readonly string $token,
        public readonly string $type,
        public readonly DateTimeImmutable $expiresAt
    ) {}
}