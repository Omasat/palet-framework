<?php

declare(strict_types=1);

namespace Tests\Database\Orm;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\ChangeTracker;
use Palet\Framework\Database\Orm\ObjectHydrator;

class TrackableEntity
{
    public int $id = 1;
    public string $name = 'Initial';
}

class ChangeTrackerTest extends TestCase
{
    public function test_detects_dirty_properties()
    {
        $hydrator = new ObjectHydrator();
        $tracker = new ChangeTracker($hydrator);
        
        $entity = new TrackableEntity();
        
        // Take snapshot
        $tracker->snapshot($entity);
        
        $this->assertFalse($tracker->isDirty($entity));
        
        // Change property
        $entity->name = 'Changed';
        
        $this->assertTrue($tracker->isDirty($entity));
        
        $dirty = $tracker->getDirtyProperties($entity);
        $this->assertArrayHasKey('name', $dirty);
        $this->assertEquals('Changed', $dirty['name']);
        $this->assertArrayNotHasKey('id', $dirty); // id did not change
    }
}
