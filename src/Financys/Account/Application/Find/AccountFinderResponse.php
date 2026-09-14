<?php

declare(strict_types=1);

namespace Financys\Account\Application\Find;

final class AccountFinderResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $code,
        public readonly string $name,
        public readonly string $balance,
        public readonly string $currency,
    ) {}
}