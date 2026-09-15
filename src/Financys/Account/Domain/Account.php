<?php

declare(strict_types=1);

namespace Financys\Account\Domain;

use Shared\Domain\Aggregate;
use Shared\Domain\Symbols;
use Shared\Domain\Uuid;

final class Account extends Aggregate
{
    public function __construct(
        private Uuid $id,
        private Uuid $userId,
        private AccountCode $code,
        private AccountName $name,
        private AccountBalance $balance
    ) {}

    public static function create(
        Uuid $id,
        Uuid $userId,
        AccountCode $code,
        AccountName $name,
        Symbols $symbol,
    ): self {
        $account = new self(
            $id,
            $userId,
            $code,
            $name,
            AccountBalance::create($symbol, '0')
        );

        $account->addEvent(AccountCreatedDomainEvent::create(
            Uuid::generate(),
            [
                'id' => $account->id(),
                'userId' => $account->userId(),
                'code' => $account->code(),
                'name' => $account->name(),
                'balance' => [
                    'symbol' => $account->balance()->symbol(),
                    'amount' => $account->balance()->amount(),
                ],
            ]
        ));

        return $account;
    }

    public function id(): string
    {
        return $this->id->value();
    }

    public function userId(): string
    {
        return $this->userId->value();
    }

    public function code(): string
    {
        return $this->code->value();
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
