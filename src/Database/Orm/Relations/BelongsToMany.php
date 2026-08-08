<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Relations;

use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;
use Palet\Framework\Database\Orm\Model\ModelCollection;

class BelongsToMany extends Relation
{
    protected string $table;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;
    protected string $parentKey;
    protected string $relatedKey;

    public function __construct(ModelInterface $related, ModelInterface $parent, string $table, string $foreignPivotKey, string $relatedPivotKey, string $parentKey, string $relatedKey)
    {
        $this->table = $table;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->parentKey = $parentKey;
        $this->relatedKey = $relatedKey;
        
        parent::__construct($related, $parent);
    }

    public function getResults(): mixed
    {
        // Mock DB fetch via pivot
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
