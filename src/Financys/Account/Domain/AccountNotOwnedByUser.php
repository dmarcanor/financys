<?php

declare(strict_types=1);

namespace Financys\Account\Domain;

final class AccountNotOwnedByUser extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Account with ID %s is not owned by the requesting user.', $id));
    }
}
