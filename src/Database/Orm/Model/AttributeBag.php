<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Model;

trait AttributeBag
{
    protected array $attributes = [];
    protected array $original = [];
    protected array $casts = [];
    
    protected AttributeCaster $caster;

    protected function bootAttributeBag(): void
    {
        $this->caster = new AttributeCaster();
    }

    public function getAttribute(string $key): mixed
    {
        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }

        $value = $this->attributes[$key];

        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        return $value;
    }

    public function setAttribute(string $key, mixed $value): static
    {
        if ($this->hasCast($key)) {
            $value = $this->caster->uncast($key, $value, $this->casts[$key]);
        }

        $this->attributes[$key] = $value;

        return $this;
    }
    
    public function hasCast(string $key): bool
    {
        return array_key_exists($key, $this->casts);
    }

    public function castAttribute(string $key, mixed $value): mixed
    {
        return $this->caster->cast($key, $value, $this->casts[$key]);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function syncOriginal(): static
    {
        $this->original = $this->attributes;
        return $this;
    }
    
    public function isDirty(): bool
    {
        return count($this->getDirty()) > 0;
    }

    public function getDirty(): array
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }
    
    public function __get(string $key): mixed
    {
        $attribute = $this->getAttribute($key);
        
        if ($attribute !== null || array_key_exists($key, $this->attributes)) {
            return $attribute;
        }
        
        if (method_exists($this, 'getRelationValue')) {
            return $this->getRelationValue($key);
        }
        
        return null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }
    
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function __unset(string $key): void
    {
        unset($this->attributes[$key]);
    }
}
