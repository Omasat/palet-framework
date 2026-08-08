<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\Relations;

use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;

interface RelationInterface
{
    /**
     * Get the results of the relationship (Lazy Load).
     */
    public function getResults(): mixed;

    /**
     * Initialize the relation on a set of models (Eager Load Setup).
     */
    public function initRelation(array $models, string $relation): array;

    /**
     * Match the eagerly loaded results to their parents.
     */
    public function match(array $models, array $results, string $relation): array;

    /**
     * Get the target model of the relationship.
     */
    public function getRelated(): ModelInterface;

    /**
     * Get the parent model of the relationship.
     */
    public function getParent(): ModelInterface;
}
