<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Relations;

use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;
use Palet\Framework\Database\Orm\Model\ModelCollection;

class HasMany extends Relation
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
        // Mock DB fetch: SELECT * FROM related WHERE foreignKey = parent->localKey
        return new ModelCollection([clone $this->related]);
    }

    public function initRelation(array $models, string $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, new ModelCollection());
        }
        return $models;
    }

    public function match(array $models, array $results, string $relation): array
    {
        return $models;
    }
}
