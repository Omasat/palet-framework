<?php

declare(strict_types=1);

namespace Tests\Database\Orm;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\ObjectHydrator;

class DummyEntity
{
    private int $id;
    protected string $name;
    public bool $active;
    
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
}

class ObjectHydratorTest extends TestCase
{
    public function test_hydrates_array_to_object_including_private_properties()
    {
        $hydrator = new ObjectHydrator();
        $entity = new DummyEntity();
        
        $data = [
            'id' => 1,
            'name' => 'John Doe',
            'active' => true,
        ];
        
        $hydrator->hydrate($data, $entity);
        
        $this->assertEquals(1, $entity->getId());
        $this->assertEquals('John Doe', $entity->getName());
        $this->assertTrue($entity->active);
    }
    
    public function test_extracts_object_to_array()
    {
        $hydrator = new ObjectHydrator();
        $entity = new DummyEntity();
        
        $hydrator->hydrate([
            'id' => 2,
            'name' => 'Jane',
            'active' => false
        ], $entity);
        
        $data = $hydrator->extract($entity);
        
        $this->assertEquals(2, $data['id']);
        $this->assertEquals('Jane', $data['name']);
        $this->assertFalse($data['active']);
    }
}
