<?php

declare(strict_types=1);

namespace Financys\Account\Application\Find;

final class AccountFinderRequest
{
    public function __construct(
        public readonly string $id,
    ) {}
}
