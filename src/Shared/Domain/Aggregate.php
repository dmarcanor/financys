<?php

declare(strict_types=1);

namespace Shared\Domain;

abstract class Aggregate
{
    protected array $events = [];

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

    public function events(): array
    {
        return $this->events;
    }
}
