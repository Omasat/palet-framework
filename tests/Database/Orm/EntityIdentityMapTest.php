<?php

declare(strict_types=1);

namespace Tests\Database\Orm;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\EntityIdentityMap;
use stdClass;

class EntityIdentityMapTest extends TestCase
{
    public function test_can_add_and_retrieve_entity()
    {
        $map = new EntityIdentityMap();
        $entity = new stdClass();
        $entity->id = 1;
        
        $map->add(stdClass::class, 1, $entity);
        
        $this->assertTrue($map->has(stdClass::class, 1));
        $this->assertSame($entity, $map->get(stdClass::class, 1));
    }
    
    public function test_can_remove_and_clear_entities()
    {
        $map = new EntityIdentityMap();
        $entity = new stdClass();
        
        $map->add(stdClass::class, 1, $entity);
        $map->remove(stdClass::class, 1);
        
        $this->assertFalse($map->has(stdClass::class, 1));
        
        $map->add(stdClass::class, 2, clone $entity);
        $map->clear();
        
        $this->assertFalse($map->has(stdClass::class, 2));
    }
}
