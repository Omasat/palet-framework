<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Scope;

use Palet\Framework\Contracts\Database\Orm\Scope\GlobalScopeInterface;

class SoftDeleteScope implements GlobalScopeInterface
{
    public function apply(mixed $builder, mixed $model): void
    {
        // builder is mocked for now
        if (method_exists($builder, 'whereNull')) {
            $builder->whereNull('deleted_at');
        } else {
            // For testing purposes
            $builder->softDeleteApplied = true;
        }
    }
}
