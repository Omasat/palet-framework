<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Schema;

interface ColumnDefinitionInterface
{
    /**
     * Dynamically set an attribute on the definition.
     */
    public function __call(string $method, array $parameters): static;

    /**
     * Get an attribute from the definition.
     */
    public function get(string $key, mixed $default = null): mixed;
    
    /**
     * Get all attributes from the definition.
     */
    public function getAttributes(): array;
}
