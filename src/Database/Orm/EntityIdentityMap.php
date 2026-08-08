<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm;

class EntityIdentityMap
{
    protected array $map = [];

    public function add(string $className, mixed $id, object $entity): void
    {
        $this->map[$className][(string)$id] = $entity;
    }

    public function get(string $className, mixed $id): ?object
    {
        return $this->map[$className][(string)$id] ?? null;
    }

    public function has(string $className, mixed $id): bool
    {
        return isset($this->map[$className][(string)$id]);
    }

    public function remove(string $className, mixed $id): void
    {
        unset($this->map[$className][(string)$id]);
    }

    public function clear(): void
    {
        $this->map = [];
    }
}
