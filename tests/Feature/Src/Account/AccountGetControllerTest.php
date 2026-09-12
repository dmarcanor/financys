<?php

declare(strict_types=1);

namespace Financys\Account\Application\Creator;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountGetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_get_an_account()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        
        $accountId = fake()->uuid();
        $accountUserId = $user->id;
        $accountCode = fake()->word();
        $accountName = fake()->name();
        $accountBalance = fake()->randomFloat();
        $accountCurrency = fake()->randomElement(['bs', 'usd']);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
            ])
            ->post('api/account', [
                'id' => $accountId,
                'userId' => $accountUserId,
                'code' => $accountCode,
                'name' => $accountName,
                'balance' => $accountBalance,
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
            'userId' => $accountUserId,
            'code' => $accountCode,
            'name' => $accountName,
            'balance' => $accountBalance,
            'currency' => $accountCurrency,
        ]);
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
        $accountBalance = fake()->randomFloat();
        $accountCurrency = fake()->randomElement(['bs', 'usd']);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
            ])
            ->post('api/account', [
                'id' => $accountId,
                'userId' => $accountUserId,
                'code' => $accountCode,
                'name' => $accountName,
                'balance' => $accountBalance,
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
        expect($json['error'])->toBe(["You are not authorized to access this account."]);
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(403);
    }

    public function test_it_should_return_error_account_not_found()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        
        $accountId = 'non-existent-account-id';

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
