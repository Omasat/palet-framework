<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Repository;

use Palet\Framework\Contracts\Database\Repository\RepositoryInterface;
use Palet\Framework\Contracts\Database\Repository\CriteriaInterface;
use RuntimeException;

abstract class BaseRepository implements RepositoryInterface
{
    protected string $modelClass;
    protected CriteriaPipeline $criteriaPipeline;
    protected mixed $queryBuilder; // Mocking QueryBuilder for now

    public function __construct(string $modelClass, mixed $queryBuilder = null)
    {
        $this->modelClass = $modelClass;
        $this->criteriaPipeline = new CriteriaPipeline();
        $this->queryBuilder = $queryBuilder;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function pushCriteria(CriteriaInterface $criteria): static
    {
        $this->criteriaPipeline->push($criteria);
        return $this;
    }

    public function applyCriteria(): mixed
    {
        return $this->criteriaPipeline->apply($this->queryBuilder);
    }
    
    public function clearCriteria(): static
    {
        $this->criteriaPipeline->clear();
        return $this;
    }

    public function find(mixed $id): ?object
    {
        $this->applyCriteria();
        // Mock execution
        return new ($this->modelClass)();
    }

    public function findOrFail(mixed $id): object
    {
        $model = $this->find($id);
        if (!$model) {
            throw new RuntimeException("Entity not found.");
        }
        return $model;
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $this->applyCriteria();
        return [new ($this->modelClass)()];
    }

    public function findOneBy(array $criteria): ?object
    {
        $this->applyCriteria();
        return new ($this->modelClass)();
    }

    public function all(): array
    {
        $this->applyCriteria();
        return [new ($this->modelClass)()];
    }

    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $this->applyCriteria();
        return [
            'data' => [new ($this->modelClass)()],
            'total' => 1,
            'per_page' => $perPage,
            'current_page' => $page
        ];
    }

    public function create(array $attributes): object
    {
        $model = new ($this->modelClass)($attributes);
        // Mock save via ORM Unit of Work ideally
        $model->save();
        return $model;
    }

    public function update(object $entity, array $attributes): object
    {
        // For testing we assume entity has a fill method (BaseModel)
        if (method_exists($entity, 'fill')) {
            $entity->fill($attributes);
            $entity->save();
        }
        return $entity;
    }

    public function delete(object $entity): bool
    {
        if (method_exists($entity, 'delete')) {
            return $entity->delete();
        }
        return true;
    }

    public function save(object $entity): bool
    {
        if (method_exists($entity, 'save')) {
            return $entity->save();
        }
        return true;
    }
}
