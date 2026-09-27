<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Account\Application\Find;

use Financys\Account\Application\Find\AccountFinderRequest;

final class AccountFinderRequestMother
{
    public static function create(
        ?string $id = null,
    ): AccountFinderRequest {
        return new AccountFinderRequest(
            $id ?? fake()->uuid,
        );
    }
}
