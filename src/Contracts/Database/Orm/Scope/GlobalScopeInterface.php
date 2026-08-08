<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\Scope;

interface GlobalScopeInterface
{
    /**
     * Apply the scope to a given query builder.
     * 
     * @param mixed $builder The QueryBuilder instance (mocked for now)
     * @param \Palet\Framework\Contracts\Database\Orm\Model\ModelInterface $model
     */
    public function apply(mixed $builder, mixed $model): void;
}
