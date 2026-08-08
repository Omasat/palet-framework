<?php

declare(strict_types=1);

namespace Palet\Framework\Pipeline;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use RuntimeException;

class PipeResolver
{
    protected ?ApplicationInterface $app;

    public function __construct(?ApplicationInterface $app = null)
    {
        $this->app = $app;
    }

    /**
     * Resolve the given pipe and its parameters.
     */
    public function resolve(mixed $pipe): array
    {
        $parameters = [];

        if (is_string($pipe)) {
            [$name, $parameters] = $this->parsePipeString($pipe);
            $pipe = $this->resolveClass($name);
        }

        if (!is_callable($pipe) && !is_object($pipe)) {
            throw new RuntimeException("Pipe must be a callable or an object.");
        }

        return [$pipe, $parameters];
    }

    protected function parsePipeString(string $pipe): array
    {
        $parameters = [];

        if (str_contains($pipe, ':')) {
            [$name, $parameters] = explode(':', $pipe, 2);
            $parameters = explode(',', $parameters);
        } else {
            $name = $pipe;
        }

        return [$name, $parameters];
    }

    protected function resolveClass(string $class): object
    {
        if ($this->app && method_exists($this->app, 'make')) {
            $resolved = $this->app->make($class);

            if (is_object($resolved)) {
                return $resolved;
            }
        }

        if (!class_exists($class)) {
            throw new RuntimeException("Pipe class [{$class}] does not exist.");
        }

        return new $class();
    }
}
