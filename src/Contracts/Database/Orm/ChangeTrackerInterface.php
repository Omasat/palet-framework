<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm;

interface ChangeTrackerInterface
{
    /**
     * Take a snapshot of the entity's current state.
     */
    public function snapshot(object $entity): void;

    /**
     * Get the properties that have changed since the last snapshot.
     */
    public function getDirtyProperties(object $entity): array;

    /**
     * Determine if the entity has any changed properties.
     */
    public function isDirty(object $entity): bool;

    /**
     * Remove the entity from tracking.
     */
    public function stopTracking(object $entity): void;
    
    /**
     * Clear all tracked entities.
     */
    public function clear(): void;
}
