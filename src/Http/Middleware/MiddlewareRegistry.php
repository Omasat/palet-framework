<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

class MiddlewareRegistry
{
    /** @var array<int, string> */
    protected array $globalMiddleware = [];

    /** @var array<string, string> */
    protected array $aliases = [];

    /** @var array<string, array<int, string>> */
    protected array $groups = [];

    public function pushGlobal(string $middleware): void
    {
        if (!in_array($middleware, $this->globalMiddleware, true)) {
            $this->globalMiddleware[] = $middleware;
        }
    }

    public function alias(string $name, string $class): void
    {
        $this->aliases[$name] = $class;
    }

    public function group(string $name, array $middleware): void
    {
        $this->groups[$name] = $middleware;
    }

    public function getGlobalMiddleware(): array
    {
        return $this->globalMiddleware;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }
}
