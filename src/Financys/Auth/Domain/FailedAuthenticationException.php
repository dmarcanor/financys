<?php

declare(strict_types= 1);

namespace Financys\Auth\Domain;

use InvalidArgumentException;

class FailedAuthenticationException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct("Unauthorized");
    }
}