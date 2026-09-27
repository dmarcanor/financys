<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Account\Application\Find;

use Financys\Account\Application\Find\AccountFinder;
use Financys\Account\Domain\AccountNotFound;
use Financys\Account\Domain\AccountRepository;
use Tests\TestCase;
use Tests\Unit\Financys\Account\Domain\AccountMother;

final class AccountFinderTest extends TestCase
{
    public function test_it_should_find_an_existing_account(): void
    {
        $request = AccountFinderRequestMother::create();
        $account = AccountMother::create(id: $request->id);
        $expectedResponse = AccountFinderResponseMother::create(
            $account->id(),
            $account->userId(),
            $account->code(),
            $account->name(),
            $account->balance()->amount(),
            $account->balance()->symbol()
        );

        $repository = mock(AccountRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with($request->id)
            ->andReturn($account);

        $response = (new AccountFinder($repository))($request);

        expect($response)->toEqual($expectedResponse);
    }

    public function test_it_should_throw_account_not_found_when_account_does_not_exist(): void
    {
        $request = AccountFinderRequestMother::create();

        $repository = mock(AccountRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with($request->id)
            ->andReturnNull();

        $this->expectException(AccountNotFound::class);
        $this->expectExceptionMessage(
            sprintf('Account with ID %s not found.', $request->id)
        );

        (new AccountFinder($repository))($request);
    }
}
