<?php

declare(strict_types=1);

namespace Tests\Unit\Src\Account\Application\Create;

use App\Models\Account as ModelsAccount;
use App\Models\User as ModelsUser;
use Financys\Account\Application\Creator\AccountCreator;
use Financys\Account\Domain\AccountEmptyCode;
use Financys\Account\Domain\AccountEmptyName;
use Financys\Account\Domain\AccountRepository;
use Financys\Account\Domain\InvalidAccountBalanceSymbolException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Shared\Domain\InvalidUuid;
use Tests\TestCase;
use Tests\Unit\Src\Account\Domain\AccountMother;

final class AccountCreatorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_should_create_an_account(): void
    {
        Event::fake();

        $request = AccountCreatorRequestMother::create();
        $expected = AccountMother::create(
            $request->id,
            $request->userId,
            $request->code,
            $request->name,
            $request->currency
        );

        ModelsUser::factory()->create(['id' => $request->userId]);
        ModelsAccount::create([
            'id' => $request->id,
            'user_id' => $request->userId,
            'code' => $request->code,
            'name' => $request->name,
            'currency' => $request->currency,
        ]);

        $repository = mock(AccountRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($this->similarTo($expected));

        (new AccountCreator($repository))($request);
    }

    public function test_it_should_throw_invalid_symbol_error(): void
    {
        $request = AccountCreatorRequestMother::create(
            currency: 'non-exist'
        );

        $this->expectException(InvalidAccountBalanceSymbolException::class);
        $this->expectExceptionMessage(
            'The account balance symbol non-exist is not valid.'
        );

        $repository = mock(AccountRepository::class);

        (new AccountCreator($repository))($request);
    }

    public function test_it_should_throw_empty_name_exception(): void
    {
        $request = AccountCreatorRequestMother::create(
            name: ''
        );

        $this->expectException(AccountEmptyName::class);
        $this->expectExceptionMessage(
            "The account name can't be empty"
        );

        $repository = mock(AccountRepository::class);

        (new AccountCreator($repository))($request);
    }

    public function test_it_should_throw_invalid_uuid_exception_when_id_is_empty(): void
    {
        $request = AccountCreatorRequestMother::create(
            id: ''
        );

        $this->expectException(InvalidUuid::class);
        $this->expectExceptionMessage('The uuid  is invalid.');

        $repository = mock(AccountRepository::class);

        (new AccountCreator($repository))($request);
    }

    public function test_it_should_throw_invalid_uuid_exception_when_user_id_is_empty(): void
    {
        $request = AccountCreatorRequestMother::create(
            userId: ''
        );

        $this->expectException(InvalidUuid::class);
        $this->expectExceptionMessage('The uuid  is invalid.');

        $repository = mock(AccountRepository::class);

        (new AccountCreator($repository))($request);
    }

    public function test_it_should_throw_empty_code_exception(): void
    {
        $request = AccountCreatorRequestMother::create(
            code: ''
        );

        $this->expectException(AccountEmptyCode::class);
        $this->expectExceptionMessage("The account code can't be empty");

        $repository = mock(AccountRepository::class);

        (new AccountCreator($repository))($request);
    }
}
