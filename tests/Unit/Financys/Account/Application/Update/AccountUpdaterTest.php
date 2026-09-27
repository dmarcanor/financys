<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Application\Update;

use Financys\Account\Application\Update\AccountUpdater;
use Financys\Account\Domain\AccountNotFound;
use Financys\Account\Domain\AccountNotOwnedByUser;
use Financys\Account\Domain\AccountRepository;
use Tests\TestCase;
use Tests\Unit\Financys\Account\Application\Update\AccountUpdaterRequestMother;
use Tests\Unit\Financys\Account\Domain\AccountMother;

final class AccountUpdaterTest extends TestCase
{
    public function test_it_should_successfully_update_an_existing_account(): void
    {
        $updateRequest = AccountUpdaterRequestMother::create();
        $account = AccountMother::create(
            id: $updateRequest->id,
            userId: $updateRequest->requestingUserId,
            code: $updateRequest->code,
            name: $updateRequest->name,
        );

        $repository = mock(AccountRepository::class);
        $repository
            ->shouldReceive('find')
            ->once()
            ->with($updateRequest->id)
            ->andReturn($account);
        $repository
            ->shouldReceive('update')
            ->once()
            ->with($this->similarTo($account))
            ->andReturnNull();

        new AccountUpdater($repository)($updateRequest);
    }

    public function test_it_should_throw_account_not_found_when_account_does_not_exist(): void
    {
        $updateRequest = AccountUpdaterRequestMother::create();

        $repository = mock(AccountRepository::class);
        $repository
            ->shouldReceive('find')
            ->once()
            ->with($updateRequest->id)
            ->andReturnNull();

        $this->expectException(AccountNotFound::class);
        $this->expectExceptionMessage(sprintf('Account with ID %s not found.', $updateRequest->id));

        new AccountUpdater($repository)($updateRequest);
    }

    public function test_it_should_not_update_an_account_not_owned_by_requesting_user(): void
    {
        $updateRequest = AccountUpdaterRequestMother::create();
        $account = AccountMother::create(id: $updateRequest->id);

        $repository = mock(AccountRepository::class);
        $repository
            ->shouldReceive('find')
            ->once()
            ->with($updateRequest->id)
            ->andReturn($account);
        $repository->shouldNotReceive('update');

        $this->expectException(AccountNotOwnedByUser::class);

        new AccountUpdater($repository)($updateRequest);
    }
}
