<?php

declare(strict_types=1);

namespace Financys\Account\Infrastructure\Postgre;

use Financys\Account\Domain\Account;
use Financys\Account\Domain\AccountBalance;
use Financys\Account\Domain\AccountCode;
use Financys\Account\Domain\AccountName;
use Financys\Account\Domain\AccountRepository;
use Illuminate\Support\Facades\DB;
use Shared\Domain\Symbols;
use Shared\Domain\Uuid;

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
                'updated_at' => now(),
            ]);
    }

    public function find(string $id): ?Account
    {
        $account = DB::table('accounts')
            ->find($id);

        if ($account === null) {
            return null;
        }

        return new Account(
            new Uuid($account->id),
            new Uuid($account->user_id),
            new AccountCode($account->code),
            new AccountName($account->name),
            new AccountBalance(
                Symbols::from($account->currency),
                (string) $account->balance,
            )
        );
    }
}
