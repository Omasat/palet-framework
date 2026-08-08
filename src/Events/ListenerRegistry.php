<?php

declare(strict_types=1);

namespace Palet\Framework\Events;

class ListenerRegistry
{
    /**
     * @var array<string, array<int, array<int, mixed>>>
     */
    protected array $listeners = [];

    /**
     * @var array<string, array<int, mixed>>
     */
    protected array $wildcards = [];

    /**
     * @var array<string, array<int, mixed>> Cache of sorted listeners
     */
    protected array $sorted = [];

    public function listen(string $event, callable|string $listener, int $priority = 0): void
    {
        if (str_contains($event, '*')) {
            $this->setupWildcardListen($event, $listener);
        } else {
            $this->listeners[$event][$priority][] = $listener;
            unset($this->sorted[$event]);
        }
    }

    protected function setupWildcardListen(string $event, callable|string $listener): void
    {
        $this->wildcards[$event][] = $listener;
        
        // Wildcard eklenirse tüm cache'i temizle (basit yaklaÅŸÄ±m)
        $this->sorted = [];
    }

    public function getListeners(string $event): array
    {
        if (isset($this->sorted[$event])) {
            return $this->sorted[$event];
        }

        $listeners = $this->listeners[$event] ?? [];
        
        // Priority descending sort
        krsort($listeners);
        
        $flatListeners = [];
        foreach ($listeners as $priorityGroup) {
            foreach ($priorityGroup as $listener) {
                $flatListeners[] = $listener;
            }
        }

        // Add wildcards
        foreach ($this->wildcards as $pattern => $wildcardListeners) {
            if ($this->matchesWildcard($event, $pattern)) {
                foreach ($wildcardListeners as $listener) {
                    $flatListeners[] = $listener;
                }
            }
        }

        return $this->sorted[$event] = $flatListeners;
    }

    protected function matchesWildcard(string $event, string $pattern): bool
    {
        $pattern = str_replace('\*', '.*', preg_quote($pattern, '#'));
        return preg_match('#^' . $pattern . '\z#u', $event) === 1;
    }

    public function hasListeners(string $event): bool
    {
        return !empty($this->getListeners($event));
    }

    public function forget(string $event): void
    {
        if (str_contains($event, '*')) {
            unset($this->wildcards[$event]);
        } else {
            unset($this->listeners[$event]);
        }
        
        unset($this->sorted[$event]);
    }
}
