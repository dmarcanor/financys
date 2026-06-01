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

        $response = $this->post('api/account', [
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
}