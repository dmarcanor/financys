<?php

declare(strict_types=1);

namespace Shared\Domain;

interface EventBus
{
    public function dispatch(DomainEvent $event);
}
