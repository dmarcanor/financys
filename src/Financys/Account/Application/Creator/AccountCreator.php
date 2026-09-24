<?php

declare(strict_types=1);

namespace Financys\Account\Application\Creator;

use App\Events\AccountCreated;
use App\Models\Account as ModelsAccount;
use Financys\Account\Domain\Account;
use Financys\Account\Domain\AccountCode;
use Financys\Account\Domain\AccountName;
use Financys\Account\Domain\AccountRepository;
use Financys\Account\Domain\InvalidAccountBalanceSymbolException;
use Shared\Domain\Symbols;
use Shared\Domain\Uuid;
use ValueError;

final class AccountCreator
{
    public function __construct(
        private readonly AccountRepository $repository
    ) {}

    public function __invoke(AccountCreatorRequest $request): void
    {
        try {
            $account = Account::create(
                new Uuid($request->id),
                new Uuid($request->userId),
                new AccountCode($request->code),
                new AccountName($request->name),
                Symbols::from($request->currency)
            );
        } catch (ValueError) {
            throw new InvalidAccountBalanceSymbolException(
                sprintf('The account balance symbol %s is not valid.', $request->currency)
            );
        }

        $this->repository->create($account);
        AccountCreated::dispatch($account);
    }
}
