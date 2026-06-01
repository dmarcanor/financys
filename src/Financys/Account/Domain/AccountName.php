<?php

declare(strict_types = 1);

namespace Financys\Account\Domain;

final class AccountName
{
    public function __construct(private string $name)
    {
        if (empty($this->name)) {
            throw new AccountEmptyName(
                sprintf("The account name can't be empty")
            );
        }
    }

    public function value() : string
    {
        return $this->name;
    }
}