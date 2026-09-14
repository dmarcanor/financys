<?php

declare(strict_types = 1);

namespace Tests\Feature\Src\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Unit\Src\Account\Domain\AccountMother;

class AccountPostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_create_an_account()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => fake()->uuid(),
                'code' => fake()->word(),
                'name' => fake()->name(),
                'balance' => $this->fakeAmount(),
                'currency' => fake()->randomElement(['bs', 'usd']),
            ]);

        $json = $response->json();

        expect($json['error'])->toBe([]);
        expect($response->status())->toBe(200);
    }

    public function test_it_should_return_unauthorized_when_no_token_is_provided()
    {
        $response = $this->post('api/account', [
            'id' => fake()->uuid(),
            'name' => fake()->name(),
            'balance' => $this->fakeAmount(),
            'currency' => fake()->randomElement(['bs', 'usd']),
        ]);

        $json = $response->json();

        expect($response->status())->toBe(401);
        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBeArray()->not->toBeEmpty();
    }

    public function test_it_should_return_unauthorized_when_token_is_invalid()
    {
        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer invalid.token.value',
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => fake()->uuid(),
                'name' => fake()->name(),
                'balance' => $this->fakeAmount(),
                'currency' => fake()->randomElement(['bs', 'usd']),
            ]);

        $json = $response->json();

        expect($response->status())->toBe(401);
        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBeArray()->not->toBeEmpty();
    }

    public function test_it_should_return_error_when_idempotency_key_is_missing()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
            ])
            ->post('api/account', [
                'id' => fake()->uuid(),
                'code' => fake()->word(),
                'name' => fake()->name(),
                'balance' => $this->fakeAmount(),
                'currency' => fake()->randomElement(['bs', 'usd']),
            ]);

        $json = $response->json();

        expect($response->status())->toBe(400);
        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBe(['Idempotency-Key header is required.']);
    }

    public function test_it_should_create_two_account_because_different_idempotency_key()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $idempotencyKey1 = 'test-idempotency-key-1';
        $idempotencyKey2 = 'test-idempotency-key-2';

        $account = AccountMother::create(userId: $user->id);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey1,
            ])
            ->post('api/account', [
                'id' => $account->id(),
                'code' => $account->code(),
                'name' => $account->name(),
                'balance' => $this->fakeAmount(),
                'currency' => $account->balance()->symbol(),
            ]);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey2,
            ])
            ->post('api/account', [
                'id' => $account->id(),
                'userId' => $account->userId(),
                'code' => $account->code(),
                'name' => $account->name(),
                'balance' => (string) $account->balance()->amount(),
                'currency' => $account->balance()->symbol(),
            ]);

        
        $accounts = DB::table('accounts')->get();

        expect($accounts->count())->toBe(1);
    }

    public function test_it_should_create_one_account_because_same_idempotency_key()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $idempotencyKey = 'test-idempotency-key';

        $account1 = AccountMother::create(userId: $user->id);
        $account2 = AccountMother::create(userId: $user->id);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', [
                'id' => $account1->id(),
                'code' => $account1->code(),
                'name' => $account1->name(),
                'balance' => (string) $account1->balance()->amount(),
                'currency' => $account1->balance()->symbol(),
            ]);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', [
                'id' => $account2->id(),
                'code' => $account2->code(),
                'name' => $account2->name(),
                'balance' => (string) $account2->balance()->amount(),
                'currency' => $account2->balance()->symbol(),
            ]);

        
        $accounts = DB::table('accounts')->get();

        expect($accounts->count())->toBe(1);
    }
}