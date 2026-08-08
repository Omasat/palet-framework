<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\UnitOfWork;

interface FlushManagerInterface
{
    /**
     * Flush all changes from the Unit of Work to the database.
     * Order of operations: Inserts -> Updates -> Deletes.
     */
    public function flush(array $newObjects, array $dirtyObjects, array $removedObjects): void;
}
