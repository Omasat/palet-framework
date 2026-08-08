<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Model;

use Palet\Framework\Contracts\Database\Orm\Relations\RelationInterface;

trait HasRelations
{
    protected array $relations = [];

    public function setRelation(string $relation, mixed $value): static
    {
        $this->relations[$relation] = $value;
        return $this;
    }

    public function getRelation(string $relation): mixed
    {
        return $this->relations[$relation];
    }

    public function relationLoaded(string $key): bool
    {
        return array_key_exists($key, $this->relations);
    }
    
    public function getRelations(): array
    {
        return $this->relations;
    }

    protected function getRelationValue(string $key): mixed
    {
        // If already loaded, return it
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If a method exists, it's likely a relationship method (Lazy Loading)
        if (method_exists($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        }

        return null;
    }

    protected function getRelationshipFromMethod(string $method): mixed
    {
        $relation = $this->$method();

        if (!$relation instanceof RelationInterface) {
            return null;
        }

        // Lazy load the results
        $results = $relation->getResults();
        
        $this->setRelation($method, $results);

        return $results;
    }
}
