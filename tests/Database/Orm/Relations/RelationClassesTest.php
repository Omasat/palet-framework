<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Relations;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Model\BaseModel;
use Palet\Framework\Database\Orm\Relations\HasOne;
use Palet\Framework\Database\Orm\Relations\HasMany;
use Palet\Framework\Database\Orm\Relations\BelongsTo;
use Palet\Framework\Database\Orm\Relations\BelongsToMany;
use Palet\Framework\Database\Orm\Relations\Pivot;
use Palet\Framework\Database\Orm\Model\ModelCollection;

class DummyParent extends BaseModel {}
class DummyRelated extends BaseModel {}

class RelationClassesTest extends TestCase
{
    public function test_has_one_relation()
    {
        $parent = new DummyParent();
        $related = new DummyRelated();
        
        $relation = new HasOne($related, $parent, 'parent_id', 'id');
        
        $this->assertInstanceOf(DummyRelated::class, $relation->getResults());
        $this->assertSame($parent, $relation->getParent());
        $this->assertSame($related, $relation->getRelated());
    }

    public function test_has_many_relation()
    {
        $parent = new DummyParent();
        $related = new DummyRelated();
        
        $relation = new HasMany($related, $parent, 'parent_id', 'id');
        
        $results = $relation->getResults();
        $this->assertInstanceOf(ModelCollection::class, $results);
    }
    
    public function test_belongs_to_relation()
    {
        $parent = new DummyParent();
        $related = new DummyRelated();
        
        $relation = new BelongsTo($related, $parent, 'related_id', 'id');
        
        $this->assertInstanceOf(DummyRelated::class, $relation->getResults());
    }

    public function test_belongs_to_many_relation()
    {
        $parent = new DummyParent();
        $related = new DummyRelated();
        
        $relation = new BelongsToMany($related, $parent, 'pivot_table', 'parent_id', 'related_id', 'id', 'id');
        
        $results = $relation->getResults();
        $this->assertInstanceOf(ModelCollection::class, $results);
    }
    
    public function test_pivot_model()
    {
        $pivot = new Pivot();
        $pivot->setKeys('user_id', 'role_id');
        
        $this->assertEquals('user_id', $pivot->getForeignKey());
        $this->assertEquals('role_id', $pivot->getRelatedKey());
    }
}
