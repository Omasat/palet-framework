<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Repository;

interface CriteriaInterface
{
    /**
     * Apply the criteria to the given QueryBuilder or mock object.
     * Since we don't have a concrete QueryBuilder class in this sprint, we use mixed.
     */
    public function apply(mixed $query): mixed;
}
