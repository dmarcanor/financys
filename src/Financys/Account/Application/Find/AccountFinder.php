<?php

declare(strict_types=1);

namespace Financys\Account\Application\Find;

use Financys\Account\Domain\AccountNotFound;
use Financys\Account\Domain\AccountRepository;

final class AccountFinder
{
    public function __construct(
        private readonly AccountRepository $repository
    ) {}

    public function __invoke(AccountFinderRequest $request): AccountFinderResponse
    {
        $account = $this->repository->find($request->id);

        if ($account === null) {
            throw new AccountNotFound($request->id);
        }

        return new AccountFinderResponse(
            $account->id(),
            $account->userId(),
            $account->code(),
            $account->name(),
            $account->balance()->amount(),
            $account->balance()->symbol()
        );
    }
}
