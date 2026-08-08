<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Events;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
    /**
     * Register a listener with the dispatcher.
     */
    public function listen(string $event, callable|string $listener, int $priority = 0): void;

    /**
     * Determine if a given event has listeners.
     */
    public function hasListeners(string $event): bool;

    /**
     * Remove a set of listeners from the dispatcher.
     */
    public function forget(string $event): void;

    /**
     * Register an event subscriber with the dispatcher.
     */
    public function subscribe(object|string $subscriber): void;
    
    /**
     * Dispatch an event until the first non-null response is returned.
     */
    public function dispatchUntil(object|string $event, mixed $payload = []): mixed;
}
