<?php

declare(strict_types=1);

namespace Tests\Feature\Financys\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Override;
use Tests\TestCase;
use Tests\Unit\Financys\Account\Domain\AccountMother;

class AccountPostControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->createUser();
    }

    public function test_it_should_create_an_account()
    {
        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->token()}",
                'Idempotency-Key' => fake()->uuid(),
            ])
            ->post('api/account', [
                'id' => fake()->uuid(),
                'code' => fake()->word(),
                'name' => fake()->name(),
                'currency' => fake()->randomElement(['bs', 'usd']),
            ]);

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
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

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBe(['Token not provided']);
        expect($response->status())->toBe(401);
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

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBe(['Could not decode token: Error while decoding from Base64Url, invalid base64 characters detected']);
        expect($response->status())->toBe(401);
    }

    public function test_it_should_return_error_when_idempotency_key_is_missing()
    {
        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->token()}",
            ])
            ->post('api/account', [
                'id' => fake()->uuid(),
                'code' => fake()->word(),
                'name' => fake()->name(),
                'currency' => fake()->randomElement(['bs', 'usd']),
            ]);

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body']);
        expect($json['body'])->toBe([]);
        expect($json['error'])->toBe(['Idempotency-Key header is required.']);
        expect($response->status())->toBe(400);
    }

    public function test_it_should_create_two_account_because_different_idempotency_key()
    {
        $account1 = AccountMother::create(userId: $this->user->id);
        $account2 = AccountMother::create(userId: $this->user->id);

        $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->token()}",
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
                'Authorization' => "Bearer {$this->token()}",
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
        $idempotencyKey = fake()->uuid();

        $account = AccountMother::create(userId: $this->user->id);
        $payload = [
            'id' => $account->id(),
            'code' => $account->code(),
            'name' => $account->name(),
            'currency' => $account->balance()->symbol(),
        ];

        $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->token()}",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', $payload);

        $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->token()}",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', $payload);

        $accounts = DB::table('accounts')->get();

        expect($accounts->count())->toBe(1);
    }

    public function test_it_should_reject_same_idempotency_key_with_different_payload()
    {
        $idempotencyKey = fake()->uuid();

        $account1 = AccountMother::create(userId: $this->user->id);
        $account2 = AccountMother::create(userId: $this->user->id);

        $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->token()}",
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
                'Authorization' => "Bearer {$this->token()}",
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post('api/account', [
                'id' => $account2->id(),
                'code' => $account2->code(),
                'name' => $account2->name(),
                'currency' => $account2->balance()->symbol(),
            ]);

        $accounts = DB::table('accounts')->get();

        expect($accounts->count())->toBe(1);
        expect($response->json('error'))->toBe(['Idempotency-Key already used with a different payload.']);
        expect($response->status())->toBe(409);
    }

    public function test_it_should_return_error_when_idempotency_key_is_not_a_uuid()
    {
        $account = AccountMother::create(userId: $this->user->id);

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->token()}",
                'Idempotency-Key' => 'not-a-uuid',
            ])
            ->post('api/account', [
                'id' => $account->id(),
                'code' => $account->code(),
                'name' => $account->name(),
                'currency' => $account->balance()->symbol(),
            ]);

        expect($response->json('error'))->toBe(['Idempotency-Key header must be a valid UUID.']);
        expect($response->status())->toBe(400);
    }
}
