<?php

declare(strict_types=1);

namespace Shared\Domain;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    public function __construct(protected string $uuid)
    {
        if (! RamseyUuid::isValid($uuid)) {
            throw new InvalidUuid($uuid);
        }
    }

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid7()->toString());
    }

    public function value(): string
    {
        return $this->uuid;
    }
}
