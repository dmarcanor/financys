<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\ApiPostController;
use Financys\Account\Application\Creator\AccountCreator;
use Financys\Account\Application\Creator\AccountCreatorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountPostController extends ApiPostController
{
    public function __construct(
        private readonly AccountCreator $accountCreator,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        return $this->idempotent($request, function () use ($request) {
            return $this->validate(function () use ($request) {
                $account = $request->validate([
                    'id' => 'required|unique:accounts,id',
                    'code' => 'required',
                    'name' => 'required',
                    'currency' => 'required',
                ]);

                ($this->accountCreator)(new AccountCreatorRequest(
                    $account['id'],
                    auth()->user()->id,
                    $account['code'],
                    $account['name'],
                    $account['currency'],
                ));

                return $this->formatResponse(
                    [],
                    [],
                    JsonResponse::HTTP_OK
                );
            });
        });
    }
}
