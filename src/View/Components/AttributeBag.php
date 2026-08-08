<?php

declare(strict_types=1);

namespace Palet\Framework\View\Components;

use Palet\Framework\Contracts\View\AttributeBagInterface;
use Stringable;

class AttributeBag implements AttributeBagInterface, Stringable
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function first(): mixed
    {
        return reset($this->attributes);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    public function merge(array $attributeDefaults = []): static
    {
        $attributes = $this->attributes;

        foreach ($attributeDefaults as $key => $value) {
            if ($key === 'class') {
                $attributes[$key] = trim(($attributes[$key] ?? '') . ' ' . $value);
            } elseif (!array_key_exists($key, $attributes)) {
                $attributes[$key] = $value;
            }
        }

        return new static($attributes);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function __toString(): string
    {
        $html = '';

        foreach ($this->attributes as $key => $value) {
            if (is_bool($value)) {
                $html .= $value ? $key . ' ' : '';
                continue;
            }

            if ($value !== null) {
                $html .= $key . '="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '" ';
            }
        }

        return trim($html);
    }
}
