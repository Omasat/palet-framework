<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Scope;

use Palet\Framework\Contracts\Database\Orm\Scope\GlobalScopeInterface;

class GlobalScopeManager
{
    protected array $scopes = [];

    public function addScope(string $modelClass, string $identifier, GlobalScopeInterface $scope): static
    {
        $this->scopes[$modelClass][$identifier] = $scope;
        return $this;
    }

    public function getScopes(string $modelClass): array
    {
        return $this->scopes[$modelClass] ?? [];
    }

    public function applyScopes(string $modelClass, mixed $builder, mixed $model): void
    {
        $scopes = $this->getScopes($modelClass);
        
        foreach ($scopes as $scope) {
            $scope->apply($builder, $model);
        }
    }
}
