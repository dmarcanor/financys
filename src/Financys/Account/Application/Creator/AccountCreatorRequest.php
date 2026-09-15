<?php

declare(strict_types=1);

namespace Financys\Account\Application\Creator;

final class AccountCreatorRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $code,
        public readonly string $name,
        public readonly string $currency
    ) {}
}
