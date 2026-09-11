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
        DB::table('accounts')
            ->insert([
                'id' => $account->id(),
                'user_id' => $account->userId(),
                'code' => $account->code(),
                'name' => $account->name(),
                'balance' => $account->balance()->amount(),
                'currency' => $account->balance()->symbol(),
                'created_at' => now(),
                'updated_at'=> now(),
            ]);
    }
}