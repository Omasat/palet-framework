<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Event;

interface EventDispatcherInterface
{
    /**
     * Dispatch an event to all registered listeners.
     */
    public function dispatch(EventInterface|string $event, mixed $payload = []): void;
    
    /**
     * Register an event listener.
     */
    public function listen(string $eventName, string|callable $listener, int $priority = 0): void;
    
    /**
     * Determine if a given event has listeners.
     */
    public function hasListeners(string $eventName): bool;
    
    /**
     * Remove a set of listeners from the dispatcher.
     */
    public function forget(string $eventName): void;
}
