<?php

declare(strict_types= 1);

namespace Tests\Unit\Financys\Application\Update;

use Financys\Account\Application\Update\AccountUpdater;
use Financys\Account\Domain\AccountNotFound;
use Financys\Account\Domain\AccountRepository;
use Tests\TestCase;
use Tests\Unit\Financys\Account\Application\Update\AccountUpdaterRequestMother;
use Tests\Unit\Financys\Account\Domain\AccountMother;

final class AccountUpdaterTest extends TestCase
{
    public function it_should_successfuly_update_an_existing_account(): void
    {
        $updateRequest = AccountUpdaterRequestMother::create();
        $account = AccountMother::create(
            id: $updateRequest->id,
            code: $updateRequest->code,
            name: $updateRequest->name,
        );

        $repository = mock(AccountRepository::class);
        $repository
            ->shouldReceive('update')
            ->once()
            ->with($this->similarTo($account))
            ->andReturnNull();

        new AccountUpdater($repository)($updateRequest);
    }

    public function it_should_throw_not_existing_account_exception(): void
    {
        $updateRequest = AccountUpdaterRequestMother::create();
        $account = AccountMother::create(
            id: $updateRequest->id,
            code: $updateRequest->code,
            name: $updateRequest->name,
        );

        $repository = mock(AccountRepository::class);
        $repository
            ->shouldReceive('update')
            ->once()
            ->with($this->similarTo($account))
            ->andReturn(null);

        $this->expectException(AccountNotFound::class);
        $this->expectExceptionMessage(sprintf('Account with ID %s not found.', $account->id()));

        new AccountUpdater($repository)($updateRequest);
    }
}