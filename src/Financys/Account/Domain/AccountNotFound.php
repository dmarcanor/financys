<?php

declare(strict_types= 1);

namespace Financys\Account\Domain;

final class AccountNotFound extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Account with ID %s not found.', $id));
    }
}