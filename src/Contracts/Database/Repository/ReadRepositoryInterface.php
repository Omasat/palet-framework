<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Repository;

interface ReadRepositoryInterface
{
    public function find(mixed $id): ?object;
    public function findOrFail(mixed $id): object;
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
    public function findOneBy(array $criteria): ?object;
    public function all(): array;
    public function paginate(int $perPage = 15, int $page = 1): array;
}
