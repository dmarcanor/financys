<?php

declare(strict_types = 1);

namespace Financys\Account\Application\Creator;

use Financys\Account\Domain\Account;
use Financys\Account\Domain\AccountRepository;

final class AccountCreator
{
    public function __construct(
        private readonly AccountRepository $repository
    ) {}

    public function __invoke(AccountCreatorRequest $request): void
    {
        $account = new Account(
            $request->id,
            $request->userId,
            $request->name,
            $request->balance,
            $request->currency
        );

        $this->repository->create($account);
    }
}