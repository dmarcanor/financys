<?php

declare(strict_types=1);

namespace Tests\Unit\Financys\Auth\Application\Authenticate;

use Financys\Auth\Application\Authenticate\Authenticator;
use Financys\Auth\Domain\AuthenticationRepository;
use Financys\Auth\Domain\FailedAuthenticationException;
use Tests\TestCase;
use Tests\Unit\Financys\Auth\Domain\AuthenticationMother;

final class AuthenticatorTest extends TestCase
{
    public function test_it_should_authenticate_and_return_response(): void
    {
        $request = AuthenticatorRequestMother::create();
        $authentication = AuthenticationMother::create();
        $expectedResponse = AuthenticatorResponseMother::create(
            $authentication->token,
            $authentication->type,
            $authentication->expiresAt->format('Y-m-d H:i:s')
        );

        $repository = mock(AuthenticationRepository::class);
        $repository->shouldReceive('authenticate')
            ->once()
            ->with($request->email, $request->password)
            ->andReturn($authentication);

        $response = (new Authenticator($repository))($request);

        expect($response)->toEqual($expectedResponse);
    }

    public function test_it_should_throw_failed_authentication_exception_when_credentials_are_invalid(): void
    {
        $request = AuthenticatorRequestMother::create();

        $repository = mock(AuthenticationRepository::class);
        $repository->shouldReceive('authenticate')
            ->once()
            ->with($request->email, $request->password)
            ->andReturn(null);

        $this->expectException(FailedAuthenticationException::class);
        $this->expectExceptionMessage('Unauthorized');

        (new Authenticator($repository))($request);
    }

    public function test_it_should_format_expires_at_as_datetime_string(): void
    {
        $request = AuthenticatorRequestMother::create();
        $authentication = AuthenticationMother::create();

        $repository = mock(AuthenticationRepository::class);
        $repository->shouldReceive('authenticate')
            ->once()
            ->andReturn($authentication);

        $response = (new Authenticator($repository))($request);

        expect($response->expiresAt)->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
    }

    public function test_it_should_return_token_type_from_authentication(): void
    {
        $request = AuthenticatorRequestMother::create();
        $authentication = AuthenticationMother::create(type: 'jwt');
        $expectedResponse = AuthenticatorResponseMother::create(
            $authentication->token,
            $authentication->type,
            $authentication->expiresAt->format('Y-m-d H:i:s')
        );

        $repository = mock(AuthenticationRepository::class);
        $repository->shouldReceive('authenticate')
            ->once()
            ->andReturn($authentication);

        $response = (new Authenticator($repository))($request);

        expect($response)->toEqual($expectedResponse);
    }
}
