<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Repository;

interface WriteRepositoryInterface
{
    public function create(array $attributes): object;
    public function update(object $entity, array $attributes): object;
    public function delete(object $entity): bool;
    public function save(object $entity): bool;
}
