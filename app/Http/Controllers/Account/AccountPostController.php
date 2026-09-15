<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\ApiController;
use Financys\Account\Application\Creator\AccountCreator;
use Financys\Account\Application\Creator\AccountCreatorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class AccountPostController extends ApiController
{
    public function __construct(
        private readonly AccountCreator $accountCreator,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        return $this->validate(function () use ($request) {
            $idempotencyKey = $request->header('Idempotency-Key');
            $cachedResponse = Cache::get("{$request->route()->uri()}-{$idempotencyKey}");

            $account = $request->validate([
                'id' => 'required|unique:accounts,id',
                'code' => 'required',
                'name' => 'required',
                'balance' => 'required|numeric|min:0|decimal:8,8',
                'currency' => 'required',
            ]);

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

            if ($cachedResponse !== null && $cachedResponse['request'] === $request->all()) {
                return $this->formatResponse(
                    $cachedResponse['response']['body'] ?? [],
                    $cachedResponse['response']['error'] ?? [],
                    $cachedResponse['response']['status'] ?? 200
                );
            }

            ($this->accountCreator)(new AccountCreatorRequest(
                $account['id'],
                auth()->user()->id,
                $account['code'],
                $account['name'],
                $account['balance'],
                $account['currency'],
            ));

            Cache::put("{$request->route()->uri()}-{$idempotencyKey}", [
                'request' => $request->all(),
                'response' => [
                    'body' => [],
                    'error' => [],
                    'status' => 200,
                ],
            ], now()->addHours(24));

            return $this->formatResponse(
                [],
                [],
                200
            );
        });
    }
}
