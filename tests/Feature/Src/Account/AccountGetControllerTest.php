<?php

declare(strict_types=1);

namespace Tests\Feature\Src\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountGetControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_should_get_an_account()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $accountId = fake()->uuid();
        $accountCode = fake()->word();
        $accountName = fake()->name();
        $accountBalance = '0.00000000';
        $accountCurrency = fake()->randomElement(['bs', 'usd']);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => $accountId,
                'code' => $accountCode,
                'name' => $accountName,
                'currency' => $accountCurrency,
            ]);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
            ])
            ->get("api/account/$accountId");

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body.id', 'body.userId', 'body.code', 'body.name', 'body.balance', 'body.currency']);
        expect($json['error'])->toBe([]);
        expect($json['body'])->toBe([
            'id' => $accountId,
            'userId' => $user->id,
            'code' => $accountCode,
            'name' => $accountName,
            'balance' => $accountBalance,
            'currency' => $accountCurrency,
        ]);
        expect($response->status())->toBe(200);
    }

    // TODO: make this test use the future update endpoint to test preserving large balance precision. The current implementation does not allow setting the balance directly, so this test may not be valid until the update endpoint is implemented.
    public function test_it_should_preserve_large_balance_precision()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $accountId = fake()->uuid();
        $accountBalance = '0.00000000';

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => $accountId,
                'code' => fake()->word(),
                'name' => fake()->name(),
                'currency' => 'usd',
            ]);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
            ])
            ->get("api/account/$accountId");

        $json = $response->json();

        expect($json['body']['balance'])->toBe($accountBalance);
        expect($response->status())->toBe(200);
    }

    public function test_it_should_return_error_unauthorized_user_account()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $accountId = fake()->uuid();
        $accountUserId = $user->id;
        $accountCode = fake()->word();
        $accountName = fake()->name();
        $accountCurrency = fake()->randomElement(['bs', 'usd']);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => $accountId,
                'userId' => $accountUserId,
                'code' => $accountCode,
                'name' => $accountName,
                'currency' => $accountCurrency,
            ]);

        $otherUser = User::factory()->create();
        $otherUserToken = auth()->login($otherUser);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $otherUserToken",
            ])
            ->get("api/account/$accountId");

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['error'])->toBe(['You are not authorized to access this account.']);
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(403);
    }

    public function test_it_should_return_error_account_not_found()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $accountId = fake()->uuid();

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
            ])
            ->get("api/account/$accountId");

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['error'])->toBe(["Account with ID {$accountId} not found."]);
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(404);
    }
}
