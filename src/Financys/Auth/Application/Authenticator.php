<?php

declare(strict_types=1);

namespace Financys\Auth\Application;

use Financys\Auth\Domain\AuthenticationRepository;
use Financys\Auth\Domain\FailedAuthenticationException;

class Authenticator
{
    public function __construct(private AuthenticationRepository $authenticationRepository) {}

    public function __invoke(AuthenticatorRequest $request): AuthenticatorResponse
    {
        $authentication = $this->authenticationRepository->authenticate(
            $request->email,
            $request->password
        );

        if ($authentication === null) {
            throw new FailedAuthenticationException();
        }

        return new AuthenticatorResponse(
            $authentication->token,
            $authentication->type,
            $authentication->expiresAt->format('Y-m-d H:i:s')
        );
    }
}
