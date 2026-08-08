<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Repository;

interface QueryObjectInterface
{
    /**
     * Execute the query and return results.
     */
    public function execute(mixed $queryBuilder): mixed;
}
