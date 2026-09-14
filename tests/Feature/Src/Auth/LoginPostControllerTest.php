<?php

declare(strict_types= 1);

namespace Tests\Feature\Src\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginPostControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_should_login_and_return_token(): void
    {
        $user = User::factory()->create(['password' => 'pass']);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password'=> 'pass',
        ]);

        $json = $response->json();

        expect($json)->toHaveKeys(['error', 'body.access_token', 'body.token_type', 'body.expires_in']);
        expect($json['error'])->toBe([]);
        expect($response->status())->toBe(200);
    }

    public function test_it_should_reject_login_and_return_unauthorized(): void
    {
        $response = $this->post('/api/login', [
            'email' => 'false@test.com',
            'password'=> 'password',
        ]);

        $json = $response->json();

        expect($json)->toBe([
            'error' => ['Unauthorized'],
            'body' => []
        ]);
        expect($response->status())->toBe(401);
    }

    public function test_it_should_validate_invalid_email(): void
    {
        $response = $this->post('/api/login', [
            'email' => 'invalidEmail',
            'password'=> 'validPassword',
        ]);

        $json = $response->json();

        expect($json)->toHaveKeys(['error.email', 'body']);
        expect($json)->not->toHaveKey('email.password');
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(400);
    }

    public function test_it_should_validate_invalid_password(): void
    {
        $response = $this->post('/api/login', [
            'email' => 'test@test.com',
            'password'=> '',
        ]);

        $json = $response->json();

        expect($json)->toHaveKeys(['error.password', 'body']);
        expect($json)->not->toHaveKey('email.email');
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(400);
    }
}