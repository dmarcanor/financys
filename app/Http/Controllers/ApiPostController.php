<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class ApiPostController extends ApiController
{
    protected function idempotent(Request $request, callable $action): JsonResponse
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        if ($idempotencyKey === null) {
            return $this->formatResponse(
                [],
                ['Idempotency-Key header is required.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if (! Uuid::isValid($idempotencyKey)) {
            return $this->formatResponse(
                [],
                ['Idempotency-Key header must be a valid UUID.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $cacheKey = "{$request->route()->uri()}-{$request->user()->id}-{$idempotencyKey}";

        return Cache::lock($cacheKey, 30)->block(10, function () use ($request, $cacheKey, $action) {
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse !== null) {
                if ($cachedResponse['request'] !== $request->all()) {
                    return $this->formatResponse(
                        [],
                        ['Idempotency-Key already used with a different payload.'],
                        JsonResponse::HTTP_CONFLICT
                    );
                }

                return $this->formatResponse(
                    $cachedResponse['response']['body'] ?? [],
                    $cachedResponse['response']['error'] ?? [],
                    $cachedResponse['response']['status'] ?? 200
                );
            }

            $response = $action();

            $responseData = $response->getData(true);

            Cache::put($cacheKey, [
                'request' => $request->all(),
                'response' => [
                    'body' => $responseData['body'] ?? [],
                    'error' => $responseData['error'] ?? [],
                    'status' => $response->getStatusCode(),
                ],
            ], now()->addHours(24));

            return $response;
        });
    }
}
