<?php

declare(strict_types=1);

namespace Shared\Domain;

class InvalidUuid extends \InvalidArgumentException
{
    public function __construct(string $uuid)
    {
        parent::__construct(sprintf('The uuid %s is invalid.', $uuid));
    }
}
