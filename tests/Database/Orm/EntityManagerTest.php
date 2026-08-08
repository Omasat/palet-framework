<?php

declare(strict_types=1);

namespace Tests\Database\Orm;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\EntityManager;
use Palet\Framework\Database\Orm\ChangeTracker;
use Palet\Framework\Database\Orm\EntityIdentityMap;
use Palet\Framework\Database\Orm\ObjectHydrator;
use Palet\Framework\Database\Orm\EntityState;

class ManagedEntity
{
    public int $id = 1;
    public string $title = 'Test';
}

class EntityManagerTest extends TestCase
{
    protected EntityManager $em;

    protected function setUp(): void
    {
        $hydrator = new ObjectHydrator();
        $tracker = new ChangeTracker($hydrator);
        $map = new EntityIdentityMap();
        $this->em = new EntityManager($tracker, $map);
    }

    public function test_entity_state_transitions()
    {
        $entity = new ManagedEntity();
        
        // NEW
        $this->assertEquals(EntityState::NEW, $this->em->getEntityState($entity));
        
        // MANAGED
        $this->em->persist($entity);
        $this->assertEquals(EntityState::MANAGED, $this->em->getEntityState($entity));
        
        // DIRTY
        $entity->title = 'Updated';
        $this->assertEquals(EntityState::DIRTY, $this->em->getEntityState($entity));
        
        // REMOVED
        $this->em->remove($entity);
        $this->assertEquals(EntityState::REMOVED, $this->em->getEntityState($entity));
        
        // DETACHED
        $this->em->detach($entity);
        $this->assertEquals(EntityState::DETACHED, $this->em->getEntityState($entity));
    }
    
    public function test_persist_adds_to_identity_map()
    {
        $entity = new ManagedEntity();
        $entity->id = 5;
        
        $this->em->persist($entity);
        
        $found = $this->em->find(ManagedEntity::class, 5);
        $this->assertSame($entity, $found);
    }
}
