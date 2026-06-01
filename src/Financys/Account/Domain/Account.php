<?php

declare(strict_types = 1);

namespace Financys\Account\Domain;

final class Account
{
    public function __construct(
        private string $id,
        private string $userId,
        private string $name,
        private float $balance,
        private string $currency,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function balance(): float
    {
        return $this->balance;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function changeBalance(float $balance): void
    {
        $this->balance = $balance;
    }
}