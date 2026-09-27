<?php

declare(strict_types= 1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\ApiController;
use Financys\Account\Application\Find\AccountFinder;
use Financys\Account\Application\Find\AccountFinderRequest;
use Financys\Account\Application\Update\AccountUpdater;
use Financys\Account\Application\Update\AccountUpdaterRequest;
use Financys\Account\Domain\AccountNotFound;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

final class AccountPutController extends ApiController
{
    public function __construct(
        public AccountUpdater $updater,
        public AccountFinder $finder,
    )
    {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        return $this->validate(function () use ($id, $request) {
            $fields = $request->validate([
                'code' => 'string|required',
                'name'=> 'string|required',
            ]);

            try {
                $account = ($this->finder)(new AccountFinderRequest($id));

                if ($request->user()->id !== $account->userId) {
                    return $this->formatResponse(
                        [],
                        ['You are not authorized to access this account.'],
                        JsonResponse::HTTP_FORBIDDEN
                    );
                }

                ($this->updater)(new AccountUpdaterRequest(
                    $id,
                    $fields['code'],
                    $fields['name'],
                ));

                return $this->formatResponse(
                    [],
                    [],
                    JsonResponse::HTTP_OK
                );
            } catch (AccountNotFound $e) {
                return $this->formatResponse(
                    [],
                    [$e->getMessage()],
                    JsonResponse::HTTP_NOT_FOUND
                );
            }
        });
    }
}