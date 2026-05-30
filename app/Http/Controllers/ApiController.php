<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController
{
    protected function formatResponse(array $body, array $error, int $code): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'body' => $body,
        ], $code);
    }
}