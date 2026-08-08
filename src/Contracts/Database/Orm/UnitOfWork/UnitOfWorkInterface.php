<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\UnitOfWork;

interface UnitOfWorkInterface
{
    /**
     * Register a new entity to be inserted.
     */
    public function registerNew(object $entity): void;

    /**
     * Register a managed entity as dirty (needing update).
     */
    public function registerDirty(object $entity): void;

    /**
     * Register an entity to be removed.
     */
    public function registerRemoved(object $entity): void;

    /**
     * Register an entity as clean.
     */
    public function registerClean(object $entity): void;

    /**
     * Commit the unit of work to the database.
     */
    public function commit(): void;

    /**
     * Clear the unit of work.
     */
    public function clear(): void;
}
