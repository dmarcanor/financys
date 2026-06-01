<?php

declare(strict_types = 1);

namespace Financys\Account\Domain;

interface AccountRepository {
    public function create(Account $account): void;
}