<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm;

use Palet\Framework\Contracts\Database\Orm\EntityManagerInterface;
use Palet\Framework\Contracts\Database\Orm\ChangeTrackerInterface;
use SplObjectStorage;
use RuntimeException;

class EntityManager implements EntityManagerInterface
{
    protected ChangeTrackerInterface $changeTracker;
    protected EntityIdentityMap $identityMap;
    
    /** @var SplObjectStorage<object, EntityState> */
    protected SplObjectStorage $entityStates;
    
    // We would have a MetadataFactory here in a full implementation to get Primary Key etc.
    // For this core, we will assume entities have an "id" property for the IdentityMap logic.

    public function __construct(ChangeTrackerInterface $changeTracker, EntityIdentityMap $identityMap)
    {
        $this->changeTracker = $changeTracker;
        $this->identityMap = $identityMap;
        $this->entityStates = new SplObjectStorage();
    }

    public function persist(object $entity): void
    {
        $state = $this->getEntityState($entity);

        if ($state === EntityState::NEW || $state === EntityState::DETACHED) {
            $this->changeTracker->snapshot($entity);
            $this->entityStates[$entity] = EntityState::MANAGED;
            
            // Add to Identity Map if it has an ID
            $id = $this->extractId($entity);
            if ($id !== null) {
                $this->identityMap->add($entity::class, $id, $entity);
            }
        } elseif ($state === EntityState::REMOVED) {
            $this->entityStates[$entity] = EntityState::MANAGED;
        }
    }

    public function remove(object $entity): void
    {
        $state = $this->getEntityState($entity);

        if ($state === EntityState::NEW || $state === EntityState::DETACHED) {
            return; // Not managed, so nothing to remove from DB conceptually.
        }

        $this->entityStates[$entity] = EntityState::REMOVED;
    }

    public function find(string $className, mixed $id): ?object
    {
        if ($this->identityMap->has($className, $id)) {
            return $this->identityMap->get($className, $id);
        }

        // Normally here we would hit the DB, create an array, and Hydrate the object.
        // For the sake of this foundation, we simulate returning null since we don't have DB records.
        return null; 
    }

    public function detach(object $entity): void
    {
        $this->changeTracker->stopTracking($entity);
        if ($this->entityStates->contains($entity)) {
            $this->entityStates[$entity] = EntityState::DETACHED;
        }
        
        $id = $this->extractId($entity);
        if ($id !== null) {
            $this->identityMap->remove($entity::class, $id);
        }
    }

    public function clear(): void
    {
        $this->changeTracker->clear();
        $this->identityMap->clear();
        
        foreach ($this->entityStates as $entity) {
            $this->entityStates[$entity] = EntityState::DETACHED;
        }
    }
    
    public function getChangeTracker(): ChangeTrackerInterface
    {
        return $this->changeTracker;
    }

    public function getEntityState(object $entity): EntityState
    {
        if ($this->entityStates->contains($entity)) {
            $state = $this->entityStates[$entity];
            
            // Auto-detect DIRTY state
            if ($state === EntityState::MANAGED && $this->changeTracker->isDirty($entity)) {
                return EntityState::DIRTY;
            }
            
            return $state;
        }

        return EntityState::NEW;
    }
    
    protected function extractId(object $entity): mixed
    {
        // Simplistic approach for prototype: assume property is named "id" and accessible or via reflection
        // In real app, we use MetadataInterface->getPrimaryKey()
        
        try {
            $reflection = new \ReflectionProperty($entity, 'id');
            $reflection->setAccessible(true);
            
            if ($reflection->isInitialized($entity)) {
                 return $reflection->getValue($entity);
            }
        } catch (\ReflectionException $e) {
            // Property doesn't exist
        }
        
        return null;
    }
}
