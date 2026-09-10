<?php

declare(strict_types=1);

namespace Financys\Account\Domain;

use Shared\Domain\DomainEvent;
use Shared\Domain\Uuid;

class AccountCreatedDomainEvent extends DomainEvent
{
    public static function create(
        Uuid $id,
        array $payload,
    ): self
    {
        return new self(
            $id,
            self::name(),
            $payload
        );
    }

    protected static function name(): string
    {
        return 'financys.account.created';
    }
}