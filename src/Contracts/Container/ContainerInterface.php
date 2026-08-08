<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Container;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Register a binding with the container.
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void;

    /**
     * Register a shared (singleton) binding in the container.
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void;

    /**
     * Register an existing instance as shared in the container.
     */
    public function instance(string $abstract, mixed $instance): mixed;

    /**
     * Alias a type to a different name.
     */
    public function alias(string $abstract, string $alias): void;

    /**
     * Resolve the given type from the container.
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /**
     * Determine if a given type is shared.
     */
    public function isShared(string $abstract): bool;
    
    /**
     * Determine if a given string is an alias.
     */
    public function isAlias(string $name): bool;
}
