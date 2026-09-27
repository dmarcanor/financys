<?php

declare(strict_types= 1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\ApiController;
use Financys\Account\Application\Update\AccountUpdater;
use Financys\Account\Application\Update\AccountUpdaterRequest;
use Financys\Account\Domain\AccountNotOwnedByUser;
use Financys\Account\Domain\AccountNotFound;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

final class AccountPutController extends ApiController
{
    public function __construct(
        public AccountUpdater $updater,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        return $this->validate(function () use ($id, $request) {
            $fields = $request->validate([
                'code' => 'string|required',
                'name'=> 'string|required',
            ]);

            try {
                ($this->updater)(new AccountUpdaterRequest(
                    $id,
                    $fields['code'],
                    $fields['name'],
                    $request->user()->id,
                ));

                return $this->formatResponse(
                    [],
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
        });
    }
}
