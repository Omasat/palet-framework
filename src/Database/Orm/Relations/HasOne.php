<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Relations;

use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;

class HasOne extends Relation
{
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(ModelInterface $related, ModelInterface $parent, string $foreignKey, string $localKey)
    {
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        
        parent::__construct($related, $parent);
    }

    public function getResults(): mixed
    {
        // Mock DB fetch: SELECT * FROM related WHERE foreignKey = parent->localKey LIMIT 1
        return clone $this->related;
    }

    public function initRelation(array $models, string $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }
        return $models;
    }

    public function match(array $models, array $results, string $relation): array
    {
        // Matches results to models based on keys for eager loading
        return $models;
    }
}
