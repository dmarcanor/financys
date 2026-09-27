<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\ApiController;
use Financys\Account\Application\Find\AccountFinder;
use Financys\Account\Application\Find\AccountFinderRequest;
use Financys\Account\Domain\AccountNotOwnedByUser;
use Financys\Account\Domain\AccountNotFound;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountGetController extends ApiController
{
    public function __construct(
        private readonly AccountFinder $accountFinder,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        try {
            $account = ($this->accountFinder)(new AccountFinderRequest($id, $request->user()->id));

            return $this->formatResponse(
                [
                    'id' => $account->id,
                    'userId' => $account->userId,
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $account->balance,
                    'currency' => $account->currency,
                ],
                [],
                JsonResponse::HTTP_OK
            );
        } catch (AccountNotFound|AccountNotOwnedByUser) {
            return $this->formatResponse(
                [],
                [sprintf('Account with ID %s not found.', $id)],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
