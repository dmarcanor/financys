<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\ApiController;
use Financys\Account\Application\Creator\AccountCreator;
use Financys\Account\Application\Creator\AccountCreatorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccountPostController extends ApiController
{
    public function __construct(
        private readonly AccountCreator $accountCreator,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        return $this->validate(function () use ($request) {
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

            return $this->formatResponse(
                [],
                [],
                200
            );
        });
    }
}
