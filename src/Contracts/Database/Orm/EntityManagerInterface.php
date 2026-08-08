<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm;

interface EntityManagerInterface
{
    /**
     * Tells the EntityManager to make an instance managed and persistent.
     */
    public function persist(object $entity): void;

    /**
     * Removes an entity instance.
     */
    public function remove(object $entity): void;

    /**
     * Finds an entity by its identifier.
     */
    public function find(string $className, mixed $id): ?object;

    /**
     * Detaches an entity from the EntityManager, causing it to stop being tracked.
     */
    public function detach(object $entity): void;

    /**
     * Clears the EntityManager. All entities that are currently managed
     * become detached.
     */
    public function clear(): void;
    
    /**
     * Get the change tracker.
     */
    public function getChangeTracker(): ChangeTrackerInterface;
}
