<?php

declare(strict_types=1);

namespace Financys\Auth\Application;

use Financys\Auth\Domain\AuthenticationRepository;

class Authenticator 
{
    public function __construct(private AuthenticationRepository $authRepository)
    {}

    public function __invoke(AuthenticatorRequest $request): AuthenticatorResponse
    {
        $authentication = $this->authRepository->authenticate(
            $request->email, 
            $request->password
        );

        return new AuthenticatorResponse(
            $authentication->userId->value(),
            $authentication->token,
            $authentication->type,
            $authentication->expiresAt->format('Y-m-d H:i:s')
        );
    }
}