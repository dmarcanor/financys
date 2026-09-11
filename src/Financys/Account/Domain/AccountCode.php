<?php

declare(strict_types= 1);

namespace Financys\Account\Domain;

use Financys\Account\Domain\AccountEmptyCode;

final class AccountCode
{
    public function __construct(private string $code)
    {
        if (empty($this->code)) {
            throw new AccountEmptyCode("The account code can't be empty");
        }
    }

    public function value() : string
    {
        return $this->code;
    }
}