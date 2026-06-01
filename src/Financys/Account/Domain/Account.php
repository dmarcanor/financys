<?php

declare(strict_types = 1);

namespace Financys\Account\Domain;

use Shared\Domain\Uuid;

final class Account
{
    public function __construct(
        private Uuid $id,
        private Uuid $userId,
        private AccountName $name,
        private AccountBalance $balance
    ) {}

    public function id(): string
    {
        return $this->id->value();
    }

    public function userId(): string
    {
        return $this->userId->value();
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function balance(): AccountBalance
    {
        return $this->balance;
    }
}