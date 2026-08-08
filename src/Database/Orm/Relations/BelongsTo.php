<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Relations;

use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;

class BelongsTo extends Relation
{
    protected string $foreignKey;
    protected string $ownerKey;

    public function __construct(ModelInterface $related, ModelInterface $parent, string $foreignKey, string $ownerKey)
    {
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
        
        parent::__construct($related, $parent);
    }

    public function getResults(): mixed
    {
        // Mock DB fetch: SELECT * FROM related WHERE ownerKey = parent->foreignKey LIMIT 1
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
        return $models;
    }
}
