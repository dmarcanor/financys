<?php

declare(strict_types= 1);

it('it should login and return token', function () {
    $response = $this->post('/api/login', [
        'email' => 'dmarcanor2@gmail.com',
        'password'=> 'password',
    ]);

    dd($response);
});