<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginPostController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->validate(function () use ($request) {
            $credentials = $request->validate([
                "email" => "required|email",
                "password" => "required",
            ]);

            $token = Auth::attempt($credentials);

            if (!$token) {
                return $this->formatResponse(
                    [],
                    ['Unauthorized'],
                    JsonResponse::HTTP_UNAUTHORIZED
                );
            }

            return $this->formatResponse(
                [
                    'access_token' => $token,
                    'token_type'   => 'bearer',
                    'expires_in'   => auth()->factory()->getTTL() * 60
                ],
                [],
                JsonResponse::HTTP_OK
            );
        });
    }
}
