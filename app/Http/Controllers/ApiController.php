<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ApiController
{
    protected function validate(callable $fun): JsonResponse
    {
        try {
            return $fun();
        } catch (ValidationException $e) {
            return $this->formatResponse(
                [],
                $e->validator->getMessageBag()->getMessages(),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    protected function formatResponse(array $body, array $error, int $code): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'body' => $body,
        ], $code);
    }
}