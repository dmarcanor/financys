<?php

declare(strict_types = 1);

namespace Shared\Domain;

use RuntimeException;

enum Processed: string {
    case YES = 'yes';
    case NO = 'no';
}

abstract class DomainEvent
{
    private const MAX_ATTEMPTS = 4;

    public function __construct(
        private Uuid $id,
        private string $name,
        private array $payload,
        private Processed $processed = Processed::NO,
        private string $response = '',
        private int $attempts = 0
    ) {
        if ($attempts > self::MAX_ATTEMPTS) {
            throw new RuntimeException(
                sprintf("Attempt %d exceeds the max attempts %d", $attempts, self::MAX_ATTEMPTS)
            );
        }
    }

    public function payload(): array
    {
        return $this->payload;
    }

    abstract protected static function name(): string;
}