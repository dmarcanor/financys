<?php

declare(strict_types = 1);

namespace Tests\Unit\Src\Account\Application\Create;

use Financys\Account\Application\Creator\AccountCreator;
use Financys\Account\Domain\Account;
use Financys\Account\Domain\AccountRepository;
use Tests\TestCase;
use Tests\Unit\Src\Account\Domain\AccountMother;

final class AccountCreatorTest extends TestCase
{
    public function test_it_should_create_an_account(): void
    {
        $request = AccountCreatorRequestMother::create();
        $account = AccountMother::create(
            $request->id,
            $request->userId,
            $request->name,
            $request->balance,
            $request->currency
        );

        $repository = mock(AccountRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->with(\Mockery::on(fn(Account $a) => 
                serialize($a) === serialize($account)
            ))
            ->andReturn(null);

        (new AccountCreator($repository))($request);
    }
}