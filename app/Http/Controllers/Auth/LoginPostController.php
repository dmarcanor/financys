<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Financys\Auth\Application\Authenticator;
use Financys\Auth\Application\AuthenticatorRequest;
use Financys\Auth\Domain\FailedAuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginPostController extends ApiController
{
    public function __construct(private Authenticator $authenticator)
    {}

    public function __invoke(Request $request): JsonResponse
    {
        return $this->validate(function () use ($request) {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            try {
                $response = ($this->authenticator)(new AuthenticatorRequest(
                    $credentials['email'], 
                    $credentials['password']
                ));

                return $this->formatResponse(
                [
                    'access_token' => $response->token,
                    'token_type' => $response->type,
                    'expires_at' => $response->expiresAt,
                ],
                [],
                JsonResponse::HTTP_OK
            );
            } catch (FailedAuthenticationException $e) {
                return $this->formatResponse(
                    [],
                    [$e->getMessage()],
                    JsonResponse::HTTP_UNAUTHORIZED
                );
            }
        });
    }
}
