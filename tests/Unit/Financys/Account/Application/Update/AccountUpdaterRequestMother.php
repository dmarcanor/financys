<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Account\Application\Update;

use Financys\Account\Application\Update\AccountUpdaterRequest;

final class AccountUpdaterRequestMother
{
    public static function create(
        ?string $id = null,
        ?string $code = null,
        ?string $name = null,
        ?string $requestingUserId = null,
    ): AccountUpdaterRequest {
        return new AccountUpdaterRequest(
            $id ?? fake()->uuid(),
            $code ?? fake()->name(),
            $name ?? fake()->name(),
            $requestingUserId ?? fake()->uuid(),
        );
    }
}
