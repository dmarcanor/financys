<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\ApiController;
use Financys\Account\Application\Creator\AccountCreator;
use Financys\Account\Application\Creator\AccountCreatorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AccountPostController extends ApiController
{
    public function __construct(
        private readonly AccountCreator $accountCreator,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $idempotencyKey = $request->header('Idempotency-Key');
        $cachedResponse = Cache::get($idempotencyKey);

        if ($idempotencyKey === null) {
            return $this->formatResponse(
                [],
                ['Idempotency-Key header is required.'],
                400
            );
        }

        if ($cachedResponse !== null) {
            return $this->formatResponse(
                $cachedResponse['body'] ?? [],
                $cachedResponse['error'] ?? [],
                $cachedResponse['status'] ?? 200
            );
        }

        return $this->validate(function () use ($request, $idempotencyKey) {
            $account = $request->validate([
                'id' => 'required',
                'userId' => 'required|exists:users,id',
                'code' => 'required',
                'name' => 'required',
                'balance' => 'required|numeric|min:0',
                'currency' => 'required',
            ]);

            ($this->accountCreator)(new AccountCreatorRequest(
                $account['id'],
                $account['userId'],
                $account['code'],
                $account['name'],
                $account['balance'],
                $account['currency'],
            ));

            Cache::put($idempotencyKey, [
                'body' => [],
                'error' => [],
                'status' => 200,
            ], now()->addMinutes(5));

            return $this->formatResponse(
                [],
                [],
                200
            );
        });
    }
}
