<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Container;

interface AliasRepositoryInterface
{
    /**
     * Map an alias to an abstract type.
     */
    public function alias(string $abstract, string $alias): void;

    /**
     * Get the abstract type for a given alias.
     */
    public function getAlias(string $alias): string;

    /**
     * Check if a given string is an alias.
     */
    public function isAlias(string $name): bool;
}
