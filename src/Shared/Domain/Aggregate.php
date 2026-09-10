<?php

declare(strict_types=1);

namespace Shared\Domain;

abstract class Aggregate
{
    protected array $events = [];

    // public function __construct(
    //     protected array $events = []
    // ) {}

    protected function addEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    public function extractEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}