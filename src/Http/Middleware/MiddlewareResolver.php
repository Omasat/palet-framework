<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

use Palet\Framework\Contracts\Http\Middleware\MiddlewareResolverInterface;
use InvalidArgumentException;

class MiddlewareResolver implements MiddlewareResolverInterface
{
    protected MiddlewareRegistry $registry;

    public function __construct(MiddlewareRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function resolve(string $name): string
    {
        $aliases = $this->registry->getAliases();

        return $aliases[$name] ?? $name;
    }

    public function resolveGroup(string $group): array
    {
        $groups = $this->registry->getGroups();

        if (!isset($groups[$group])) {
            throw new InvalidArgumentException("Middleware group [{$group}] does not exist.");
        }

        $resolved = [];
        foreach ($groups[$group] as $middleware) {
            $resolved[] = $this->resolve($middleware);
        }

        return $resolved;
    }
}
