<?php

declare(strict_types=1);

namespace Palet\Framework\Events;

use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use RuntimeException;

class EventDispatcher implements EventDispatcherInterface
{
    protected ListenerRegistry $registry;
    protected EventResolver $resolver;

    protected int $maxDepth = 50;
    protected int $currentDepth = 0;

    public function __construct(ListenerRegistry $registry = null, EventResolver $resolver = null)
    {
        $this->registry = $registry ?? new ListenerRegistry();
        $this->resolver = $resolver ?? new EventResolver();
    }

    /**
     * Provide the dispatcher with a full IoC application instance.
     */
    public static function createWithApplication(ApplicationInterface $app): self
    {
        return new self(
            new ListenerRegistry(),
            new EventResolver($app)
        );
    }

    /**
     * Register a listener with the dispatcher.
     */
    public function listen(string $event, callable|string $listener, int $priority = 0): void
    {
        $this->registry->listen($event, $listener, $priority);
    }

    /**
     * Determine if a given event has listeners.
     */
    public function hasListeners(string $event): bool
    {
        return $this->registry->hasListeners($event);
    }

    /**
     * Remove a set of listeners from the dispatcher.
     */
    public function forget(string $event): void
    {
        $this->registry->forget($event);
    }

    /**
     * Register an event subscriber with the dispatcher.
     */
    public function subscribe(object|string $subscriber): void
    {
        if (is_string($subscriber)) {
            $subscriber = $this->resolver->resolveClass($subscriber);
        }
        
        if (is_object($subscriber) && method_exists($subscriber, 'subscribe')) {
            $subscriber->subscribe($this);
        }
    }

    /**
     * Provide all relevant listeners with an event to process.
     * (PSR-14 compliant)
     */
    public function dispatch(object $event): object
    {
        $this->incrementDepth();

        $eventName = get_class($event);
        $listeners = $this->registry->getListeners($eventName);

        foreach ($listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $callable = $this->resolver->resolve($listener);
            $callable($event);
        }

        $this->decrementDepth();

        return $event;
    }

    /**
     * Dispatch an event until the first non-null response is returned.
     * Not PSR-14 compliant exactly, but useful for framework events.
     */
    public function dispatchUntil(object|string $event, mixed $payload = []): mixed
    {
        $this->incrementDepth();

        $eventName = is_string($event) ? $event : get_class($event);
        $eventObject = is_string($event) ? (object) $payload : $event;

        $listeners = $this->registry->getListeners($eventName);

        foreach ($listeners as $listener) {
            $callable = $this->resolver->resolve($listener);
            $response = $callable($eventObject, $payload);

            if ($response !== null) {
                $this->decrementDepth();
                return $response;
            }
        }

        $this->decrementDepth();
        return null;
    }

    protected function incrementDepth(): void
    {
        $this->currentDepth++;

        if ($this->currentDepth > $this->maxDepth) {
            throw new RuntimeException("Event dispatch maximum depth ({$this->maxDepth}) reached. Possible infinite loop.");
        }
    }

    protected function decrementDepth(): void
    {
        $this->currentDepth--;
    }
}
