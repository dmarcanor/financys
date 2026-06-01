<?php

declare(strict_types = 1);

namespace Financys\Account\Infrastructure\Postgre;

use DB;
use Financys\Account\Domain\Account;
use Financys\Account\Domain\AccountRepository;

class AccountPostgreRepository implements AccountRepository
{


    public function create(Account $account): void
    {
        DB::transaction(function () use ($account) {
            $t = DB::statement("INSERT INTO accounts (id, user_id, name, balance, currency) VALUES ('{$account->id()}', '{$account->userId()}', '{$account->name()}', {$account->balance()}, '{$account->currency()}');");
            });
    }
}