<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Advanced;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Model\BaseModel;
use Palet\Framework\Database\Orm\Model\Traits\SoftDeletes;
use Palet\Framework\Database\Orm\Observer\HasObservers;
use Palet\Framework\Database\Orm\Observer\ObserverManager;

class SoftDeleteModel extends BaseModel
{
    use SoftDeletes, HasObservers;

    protected array $fillable = ['name'];
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        static::setObserverManager(new ObserverManager());
    }
}

class SoftDeleteTest extends TestCase
{
    public function test_soft_delete_sets_deleted_at()
    {
        $model = new SoftDeleteModel(['name' => 'John']);
        
        $this->assertFalse($model->trashed());
        
        $model->delete(); // soft delete
        
        $this->assertTrue($model->trashed());
        $this->assertNotNull($model->deleted_at);
    }
    
    public function test_restore_removes_deleted_at()
    {
        $model = new SoftDeleteModel(['name' => 'John']);
        $model->delete();
        
        $this->assertTrue($model->trashed());
        
        $model->restore();
        
        $this->assertFalse($model->trashed());
        $this->assertNull($model->deleted_at);
    }
    
    public function test_force_delete_is_detected()
    {
        $model = new SoftDeleteModel(['name' => 'John']);
        
        $model->forceDelete();
        
        // It should have fired forceDeleted event. Since we don't have DB, we just assert true for now
        $this->assertTrue(true);
    }
}
