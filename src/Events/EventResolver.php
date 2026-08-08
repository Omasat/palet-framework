<?php

declare(strict_types=1);

namespace Palet\Framework\Events;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use RuntimeException;

class EventResolver
{
    protected ?ApplicationInterface $app;

    public function __construct(?ApplicationInterface $app = null)
    {
        $this->app = $app;
    }

    /**
     * Resolve the listener from a string or callable.
     */
    public function resolve(callable|string $listener): callable
    {
        if (is_callable($listener)) {
            return $listener;
        }

        if (is_string($listener)) {
            return $this->createClassCallable($listener);
        }

        throw new RuntimeException('Listener must be a callable or string.');
    }

    protected function createClassCallable(string $listener): callable
    {
        $segments = explode('@', $listener);
        $class = $segments[0];
        $method = $segments[1] ?? 'handle';

        return function (object $event, mixed $payload = []) use ($class, $method) {
            $instance = $this->resolveClass($class);
            return $instance->$method($event, $payload);
        };
    }

    public function resolveClass(string $class): object
    {
        if ($this->app && method_exists($this->app, 'make')) {
            return $this->app->make($class);
        }

        return new $class();
    }
}
