<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\UnitOfWork;

use Palet\Framework\Contracts\Database\Orm\UnitOfWork\UnitOfWorkInterface;
use Palet\Framework\Contracts\Database\Orm\UnitOfWork\FlushManagerInterface;
use SplObjectStorage;

class UnitOfWork implements UnitOfWorkInterface
{
    protected SplObjectStorage $newObjects;
    protected SplObjectStorage $dirtyObjects;
    protected SplObjectStorage $removedObjects;
    protected SplObjectStorage $cleanObjects;
    
    protected FlushManagerInterface $flushManager;

    public function __construct(FlushManagerInterface $flushManager)
    {
        $this->flushManager = $flushManager;
        $this->clear();
    }

    public function registerNew(object $entity): void
    {
        if ($this->dirtyObjects->contains($entity) || $this->removedObjects->contains($entity) || $this->cleanObjects->contains($entity)) {
            return;
        }
        $this->newObjects->attach($entity);
    }

    public function registerDirty(object $entity): void
    {
        if ($this->removedObjects->contains($entity)) {
            return;
        }
        if (!$this->newObjects->contains($entity) && !$this->dirtyObjects->contains($entity)) {
            $this->dirtyObjects->attach($entity);
        }
    }

    public function registerRemoved(object $entity): void
    {
        if ($this->newObjects->contains($entity)) {
            $this->newObjects->detach($entity);
            return;
        }
        $this->dirtyObjects->detach($entity);
        $this->removedObjects->attach($entity);
    }

    public function registerClean(object $entity): void
    {
        $this->newObjects->detach($entity);
        $this->dirtyObjects->detach($entity);
        $this->removedObjects->detach($entity);
        $this->cleanObjects->attach($entity);
    }

    public function commit(): void
    {
        $new = $this->extract($this->newObjects);
        $dirty = $this->extract($this->dirtyObjects);
        $removed = $this->extract($this->removedObjects);

        if (empty($new) && empty($dirty) && empty($removed)) {
            return;
        }

        $this->flushManager->flush($new, $dirty, $removed);
        
        foreach ($new as $obj) $this->registerClean($obj);
        foreach ($dirty as $obj) $this->registerClean($obj);
        foreach ($removed as $obj) $this->cleanObjects->detach($obj); // It's gone
        
        $this->newObjects = new SplObjectStorage();
        $this->dirtyObjects = new SplObjectStorage();
        $this->removedObjects = new SplObjectStorage();
    }

    public function clear(): void
    {
        $this->newObjects = new SplObjectStorage();
        $this->dirtyObjects = new SplObjectStorage();
        $this->removedObjects = new SplObjectStorage();
        $this->cleanObjects = new SplObjectStorage();
    }
    
    protected function extract(SplObjectStorage $storage): array
    {
        $items = [];
        foreach ($storage as $obj) {
            $items[] = $obj;
        }
        return $items;
    }
}
