<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Middleware;

interface MiddlewareResolverInterface
{
    /**
     * Resolve a middleware name to its fully qualified class name.
     */
    public function resolve(string $name): string;

    /**
     * Resolve a middleware group to its array of middleware class names.
     */
    public function resolveGroup(string $group): array;
}
