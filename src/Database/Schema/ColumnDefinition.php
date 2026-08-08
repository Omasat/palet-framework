<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Schema;

use Palet\Framework\Contracts\Database\Schema\ColumnDefinitionInterface;

class ColumnDefinition implements ColumnDefinitionInterface
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __call(string $method, array $parameters): static
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;
        
        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
    
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
