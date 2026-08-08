<?php

declare(strict_types=1);

namespace Tests\Database\Repository;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Repository\BaseRepository;
use stdClass;

class DummyEntity
{
    public $id;
    public $saved = false;
    public $deleted = false;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function save()
    {
        $this->saved = true;
        return true;
    }
    
    public function delete()
    {
        $this->deleted = true;
        return true;
    }
    
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }
}

class DummyRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(DummyEntity::class, new stdClass());
    }
}

class RepositoryTest extends TestCase
{
    public function test_repository_creates_entity()
    {
        $repo = new DummyRepository();
        $entity = $repo->create(['id' => 1]);
        
        $this->assertInstanceOf(DummyEntity::class, $entity);
        $this->assertEquals(1, $entity->id);
        $this->assertTrue($entity->saved);
    }
    
    public function test_repository_updates_entity()
    {
        $repo = new DummyRepository();
        $entity = new DummyEntity(['id' => 1]);
        
        $repo->update($entity, ['id' => 2]);
        
        $this->assertEquals(2, $entity->id);
        $this->assertTrue($entity->saved);
    }
    
    public function test_repository_deletes_entity()
    {
        $repo = new DummyRepository();
        $entity = new DummyEntity(['id' => 1]);
        
        $repo->delete($entity);
        
        $this->assertTrue($entity->deleted);
    }
    
    public function test_repository_find_methods_return_model_instances()
    {
        $repo = new DummyRepository();
        
        $entity = $repo->find(1);
        $this->assertInstanceOf(DummyEntity::class, $entity);
        
        $all = $repo->all();
        $this->assertIsArray($all);
        $this->assertInstanceOf(DummyEntity::class, $all[0]);
    }
}
