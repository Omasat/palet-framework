<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\UrlGenerator;

class UrlDefaults
{
    protected array $defaults = [];

    public function add(string $key, mixed $value): void
    {
        $this->defaults[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->defaults[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->defaults;
    }
}
