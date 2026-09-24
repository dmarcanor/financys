<?php

declare(strict_types= 1);

namespace Shared\Infrastructure\EventBus;

use Shared\Domain\DomainEvent;
use Shared\Domain\EventBus;

class LaravelEventBus implements EventBus
{
    public function dispatch(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $eventClassName = str_replace('DomainEvent', '', class_basename($event));
            $files = glob(base_path("app/Events/*/*{$eventClassName}*"));
            
            if (empty($files)) {
                continue;
            }
            
            // Extract full class name from file path
            $filePath = $files[0];
            $relativePath = str_replace(base_path('app/'), '', $filePath);
            $className = 'App\\' . str_replace(['/', '.php'], ['\\', ''], $relativePath);
            
            // No require_once needed - Composer handles autoloading
            if (class_exists($className)) {
                $laravelEvent = new $className($event);
                $laravelEvent::dispatch($event);
            }
        }
    }
}