<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Relations;

use Palet\Framework\Contracts\Database\Orm\Relations\RelationInterface;
use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;

abstract class Relation implements RelationInterface
{
    protected ModelInterface $related;
    protected ModelInterface $parent;

    public function __construct(ModelInterface $related, ModelInterface $parent)
    {
        $this->related = $related;
        $this->parent = $parent;
    }

    public function getRelated(): ModelInterface
    {
        return $this->related;
    }

    public function getParent(): ModelInterface
    {
        return $this->parent;
    }

    abstract public function getResults(): mixed;
    abstract public function initRelation(array $models, string $relation): array;
    abstract public function match(array $models, array $results, string $relation): array;
}
