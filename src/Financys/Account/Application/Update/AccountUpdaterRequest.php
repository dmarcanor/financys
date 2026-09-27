<?php

declare(strict_types=1);

namespace Financys\Account\Application\Update;

final class AccountUpdaterRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $name,
        public readonly string $requestingUserId,
    ) {}
}
