<?php

declare(strict_types= 1);

namespace Financys\Account\Application\Update;

use Financys\Account\Domain\AccountCode;
use Financys\Account\Domain\AccountName;
use Financys\Account\Domain\AccountNotFound;
use Financys\Account\Domain\AccountRepository;
use Shared\Domain\Uuid;

final class AccountUpdater
{
    public function __construct(
        private AccountRepository $accountRepository,
    ) {}

    public function __invoke(AccountUpdaterRequest $request): void
    {
        $account = $this->accountRepository->find($request->id);

        if ($account === null) {
            throw new AccountNotFound($request->id);
        }

        $account->assertOwnedBy(new Uuid($request->requestingUserId));

        $account->rename(new AccountName($request->name));
        $account->changeCode(new AccountCode($request->code));

        $this->accountRepository->update($account);
    }
}
