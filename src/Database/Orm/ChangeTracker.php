<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm;

use Palet\Framework\Contracts\Database\Orm\ChangeTrackerInterface;
use Palet\Framework\Contracts\Database\Orm\HydratorInterface;
use SplObjectStorage;

class ChangeTracker implements ChangeTrackerInterface
{
    protected HydratorInterface $hydrator;
    
    /** @var SplObjectStorage<object, array> */
    protected SplObjectStorage $snapshots;

    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        $this->snapshots = new SplObjectStorage();
    }

    public function snapshot(object $entity): void
    {
        $this->snapshots[$entity] = $this->hydrator->extract($entity);
    }

    public function getDirtyProperties(object $entity): array
    {
        if (!$this->snapshots->contains($entity)) {
            return $this->hydrator->extract($entity); // All properties are "new/dirty"
        }

        $original = $this->snapshots[$entity];
        $current = $this->hydrator->extract($entity);
        $dirty = [];

        foreach ($current as $key => $value) {
            if (!array_key_exists($key, $original) || $original[$key] !== $value) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    public function isDirty(object $entity): bool
    {
        return count($this->getDirtyProperties($entity)) > 0;
    }

    public function stopTracking(object $entity): void
    {
        if ($this->snapshots->contains($entity)) {
            $this->snapshots->detach($entity);
        }
    }
    
    public function clear(): void
    {
        $this->snapshots = new SplObjectStorage();
    }
}
