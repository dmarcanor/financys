<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginPostController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        try{
            $credentials = $request->validate([
                "email"=> "required|email",
                "password"=> "required",
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
        } catch(ValidationException $e){  
            return $this->formatResponse(
                [],
                $e->validator->getMessageBag()->getMessages(),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
