<?php

declare(strict_types=1);

namespace Palet\Framework\Event;

use Palet\Framework\Contracts\Event\EventInterface;
use Palet\Framework\Contracts\Event\EventDispatcherInterface;
use Palet\Framework\Contracts\Event\ListenerInterface;
use Palet\Framework\Contracts\Event\SubscriberInterface;

class GenericEvent implements EventInterface
{
    protected string $name;
    protected array $payload;

    public function __construct(string $name, array $payload = [])
    {
        $this->name = $name;
        $this->payload = $payload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}

class EventDispatcher implements EventDispatcherInterface
{
    /** @var array<string, array<array{listener: string|callable, priority: int}>> */
    protected array $listeners = [];

    public function dispatch(EventInterface|string $event, mixed $payload = []): void
    {
        if (is_string($event)) {
            $event = new GenericEvent($event, is_array($payload) ? $payload : [$payload]);
        }
        
        $eventName = $event->getName();
        $listeners = $this->getListenersForEvent($eventName);
        
        foreach ($listeners as $listenerDef) {
            $listener = $listenerDef['listener'];
            
            if ($listener instanceof ListenerInterface) {
                $listener->handle($event);
            } elseif (is_callable($listener)) {
                $listener($event);
            } elseif (is_string($listener) && class_exists($listener)) {
                $instance = new $listener();
                if ($instance instanceof ListenerInterface) {
                    $instance->handle($event);
                } elseif (method_exists($instance, 'handle')) {
                    $instance->handle($event);
                }
            }
        }
    }
    
    public function listen(string $eventName, string|callable|ListenerInterface $listener, int $priority = Priority::NORMAL): void
    {
        $this->listeners[$eventName][] = [
            'listener' => $listener,
            'priority' => $priority
        ];
        
        // Sort immediately or lazily. Let's do it immediately for simplicity
        usort($this->listeners[$eventName], function ($a, $b) {
            return $b['priority'] <=> $a['priority']; // Higher priority first
        });
    }
    
    public function subscribe(SubscriberInterface $subscriber): void
    {
        $subscriber->subscribe($this);
    }
    
    public function hasListeners(string $eventName): bool
    {
        return count($this->getListenersForEvent($eventName)) > 0;
    }
    
    public function forget(string $eventName): void
    {
        unset($this->listeners[$eventName]);
    }
    
    protected function getListenersForEvent(string $eventName): array
    {
        $matchedListeners = $this->listeners[$eventName] ?? [];
        
        // Handle wildcards
        foreach ($this->listeners as $registeredEventName => $listeners) {
            if ($registeredEventName === $eventName) {
                continue;
            }
            
            if (str_contains($registeredEventName, '*') && fnmatch($registeredEventName, $eventName)) {
                $matchedListeners = array_merge($matchedListeners, $listeners);
            }
        }
        
        usort($matchedListeners, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        return $matchedListeners;
    }
}
