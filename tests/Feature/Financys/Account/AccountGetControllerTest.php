<?php

declare(strict_types=1);

namespace Tests\Feature\Financys\Account;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Financys\Account\Domain\AccountMother;

final class AccountGetControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->createUser();
    }

    public function test_it_should_get_an_account(): void
    {
        $account = AccountMother::create(userId: $this->user->id);

        Account::factory()->create([
            'id' => $account->id(),
            'user_id' => $account->userId(),
            'name' => $account->name(),
            'code' => $account->code(),
            'balance' => $account->balance()->amount(),
            'currency' => $account->balance()->symbol(),
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$this->token()}")
            ->get("api/account/{$account->id()}");

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body.id', 'body.userId', 'body.code', 'body.name', 'body.balance', 'body.currency']);
        expect($json['error'])->toBe([]);
        expect($json['body'])->toBe([
            'id' => $account->id(),
            'userId' => $this->user->id,
            'code' => $account->code(),
            'name' => $account->name(),
            'balance' => $account->balance()->amount(),
            'currency' => $account->balance()->symbol(),
        ]);
        expect($response->status())->toBe(200);
    }

    public function test_it_should_preserve_large_balance_precision(): void
    {
        $account = AccountMother::create(userId: $this->user->id, currency: 'usd');
        $largeBalance = '999999999.99999999';

        Account::factory()->create([
            'id' => $account->id(),
            'user_id' => $account->userId(),
            'name' => $account->name(),
            'code' => $account->code(),
            'balance' => $largeBalance,
            'currency' => $account->balance()->symbol(),
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$this->token()}")
            ->get("api/account/{$account->id()}");

        $json = $response->json();

        expect($json['body']['balance'])->toBe($largeBalance);
        expect($response->status())->toBe(200);
    }

    public function test_it_should_return_error_unauthorized_user_account(): void
    {
        $account = AccountMother::create(userId: $this->user->id);

        Account::factory()->create([
            'id' => $account->id(),
            'user_id' => $account->userId(),
            'name' => $account->name(),
            'code' => $account->code(),
            'balance' => $account->balance()->amount(),
            'currency' => $account->balance()->symbol(),
        ]);

        $otherUser = User::factory()->create();
        $otherUserToken = auth()->login($otherUser);

        $response = $this
            ->withHeader('Authorization', "Bearer {$otherUserToken}")
            ->get("api/account/{$account->id()}");

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBe(['You are not authorized to access this account.']);
        expect($response->status())->toBe(403);
    }

    public function test_it_should_return_error_account_not_found(): void
    {
        $accountId = fake()->uuid();

        $response = $this
            ->withHeader('Authorization', "Bearer {$this->token()}")
            ->get("api/account/{$accountId}");

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBe(["Account with ID {$accountId} not found."]);
        expect($response->status())->toBe(404);
    }
}
