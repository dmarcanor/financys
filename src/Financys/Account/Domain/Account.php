<?php

declare(strict_types = 1);

namespace Financys\Account\Domain;

use Shared\Domain\Aggregate;
use Shared\Domain\Uuid;

final class Account extends Aggregate
{
    public function __construct(
        private Uuid $id,
        private Uuid $userId,
        private AccountName $name,
        private AccountBalance $balance
    ) {}

    public static function create(
        Uuid $id,
        Uuid $userId,
        AccountName $name,
        AccountBalance $balance
    ): self
    {
        $account = new self(
            $id,
            $userId,
            $name,
            $balance
        );

        $account->addEvent(AccountCreatedDomainEvent::create(
            Uuid::generate(),
            [
                'id' => $account->id(),
                'userId' => $account->userId(),
                'name' => $account->name(),
                'balance' => [
                    'symbol' => $account->balance()->symbol(),
                    'amount' => $account->balance()->amount()
                ]
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

    public function name(): string
    {
        return $this->name->value();
    }

    public function balance(): AccountBalance
    {
        return $this->balance;
    }
}