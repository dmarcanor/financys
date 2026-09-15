<?php

declare(strict_types=1);

namespace Tests\Feature\Src\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Unit\Src\Account\Domain\AccountMother;

class AccountPostControllerTest extends TestCase
{
    use DatabaseTransactions;

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

        $account1 = AccountMother::create(userId: $user->id);
        $account2 = AccountMother::create(userId: $user->id);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => $account1->id(),
                'code' => $account1->code(),
                'name' => $account1->name(),
                'currency' => $account1->balance()->symbol(),
            ])
            ->assertStatus(200);

        $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => $account2->id(),
                'code' => $account2->code(),
                'name' => $account2->name(),
                'currency' => $account2->balance()->symbol(),
            ])
            ->assertStatus(200);

        $accounts = DB::table('accounts')->get();

        expect($accounts->count())->toBe(2);
    }

    public function test_it_should_create_one_account_because_same_idempotency_key_and_payload()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $idempotencyKey = fake()->uuid();

        $account = AccountMother::create(userId: $user->id);
        $payload = [
            'id' => $account->id(),
            'code' => $account->code(),
            'name' => $account->name(),
            'currency' => $account->balance()->symbol(),
        ];

        $firstResponse = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', $payload);

        $secondResponse = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', $payload);

        $accounts = DB::table('accounts')->get();

        expect($firstResponse->status())->toBe(200);
        expect($secondResponse->status())->toBe(200);
        expect($secondResponse->json())->toBe($firstResponse->json());
        expect($accounts->count())->toBe(1);
    }

    public function test_it_should_reject_same_idempotency_key_with_different_payload()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $idempotencyKey = fake()->uuid();

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
                'currency' => $account1->balance()->symbol(),
            ])
            ->assertStatus(200);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', [
                'id' => $account2->id(),
                'code' => $account2->code(),
                'name' => $account2->name(),
                'currency' => $account2->balance()->symbol(),
            ]);

        expect($response->status())->toBe(409);
        expect($response->json('error'))->toBe(['Idempotency-Key already used with a different payload.']);

        $accounts = DB::table('accounts')->get();

        expect($accounts->count())->toBe(1);
    }

    public function test_it_should_return_error_when_idempotency_key_is_not_a_uuid()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $account = AccountMother::create(userId: $user->id);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer $token",
                'Idempotency-Key' => 'not-a-uuid',
            ])
            ->post('api/account', [
                'id' => $account->id(),
                'code' => $account->code(),
                'name' => $account->name(),
                'currency' => $account->balance()->symbol(),
            ]);

        expect($response->status())->toBe(400);
        expect($response->json('error'))->toBe(['Idempotency-Key header must be a valid UUID.']);
    }
}
