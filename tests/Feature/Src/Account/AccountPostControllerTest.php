<?php

declare(strict_types = 1);

namespace Tests\Feature\Src\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
            ])
            ->post('api/account', [
                'id' => fake()->uuid(),
                'userId' => $user->id,
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
}