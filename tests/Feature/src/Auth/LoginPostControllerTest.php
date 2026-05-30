<?php

declare(strict_types= 1);

it('it should login and return token', function () {
    $response = $this->post('/api/login', [
        'email' => 'test@test.com',
        'password'=> 'password',
    ]);

    $json = $response->json();

    expect($json)->toHaveKeys(['error', 'body.access_token', 'body.token_type', 'body.expires_in']);
    expect($json['error'])->toBe([]);
    expect($response->status())->toBe(200);
});

it('it should reject login and return unauthorized', function () {
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
});

it('it should validate invalid email input', function () {
    $response = $this->post('/api/login', [
        'email' => 'invalidEmail',
        'password'=> 'validPassword',
    ]);

    $json = $response->json();

    expect($json)->toHaveKeys(['error.email', 'body']);
    expect($json)->not->toHaveKey('email.password');
    expect($json['body'])->toBe([]);
    expect($response->status())->toBe(400);
});

it('it should validate invalid password input', function () {
    $response = $this->post('/api/login', [
        'email' => 'test@test.com',
        'password'=> '',
    ]);

    $json = $response->json();

    expect($json)->toHaveKeys(['error.password', 'body']);
    expect($json)->not->toHaveKey('email.email');
    expect($json['body'])->toBe([]);
    expect($response->status())->toBe(400);
});