<?php

declare(strict_types = 1);

namespace Tests\Feature\Src\Account;

use App\Models\User;
use Financys\Account\Domain\AccountRepository;
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
                'userId' => $user->id,
                'code' => fake()->word(),
                'name' => fake()->name(),
                'balance' => fake()->randomFloat(),
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
            'balance' => fake()->randomFloat(),
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
                'balance' => fake()->randomFloat(),
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
                'userId' => $user->id,
                'code' => fake()->word(),
                'name' => fake()->name(),
                'balance' => fake()->randomFloat(),
                'currency' => fake()->randomElement(['bs', 'usd']),
            ]);

        $json = $response->json();

        expect($response->status())->toBe(400);
        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBe(['Idempotency-Key header is required.']);
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
                'userId' => $account1->userId(),
                'code' => $account1->code(),
                'name' => $account1->name(),
                'balance' => $account1->balance()->amount(),
                'currency' => $account1->balance()->symbol(),
            ]);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', [
                'id' => $account2->id(),
                'userId' => $account2->userId(),
                'code' => $account2->code(),
                'name' => $account2->name(),
                'balance' => $account2->balance()->amount(),
                'currency' => $account2->balance()->symbol(),
            ]);

        
        $accounts = DB::table('accounts')->get();

        expect($accounts->count())->toBe(1);
    }
}