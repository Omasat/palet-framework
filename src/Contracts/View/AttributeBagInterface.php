<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

interface AttributeBagInterface
{
    /**
     * Get the first attribute's value.
     */
    public function first(): mixed;

    /**
     * Get a given attribute from the attribute array.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Determine if a given attribute exists in the attribute array.
     */
    public function has(string $key): bool;

    /**
     * Merge additional attributes / values into the attribute bag.
     */
    public function merge(array $attributeDefaults = []): static;

    /**
     * Return all attributes.
     */
    public function getAttributes(): array;
}
