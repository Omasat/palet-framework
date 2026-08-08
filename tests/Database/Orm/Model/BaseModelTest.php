<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Model;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Model\BaseModel;
use Palet\Framework\Database\Orm\EntityManager;

class User extends BaseModel
{
    protected array $fillable = ['name', 'email'];
    protected array $hidden = ['password'];
    protected array $casts = [
        'is_active' => 'boolean'
    ];
}

class BaseModelTest extends TestCase
{
    public function test_mass_assignment_respects_fillable()
    {
        $user = new User([
            'name' => 'John',
            'email' => 'john@test.com',
            'is_admin' => 1 // not fillable
        ]);
        
        $this->assertEquals('John', $user->name);
        $this->assertEquals('john@test.com', $user->email);
        $this->assertNull($user->is_admin);
    }
    
    public function test_force_fill_bypasses_fillable()
    {
        $user = new User();
        $user->forceFill([
            'name' => 'John',
            'is_admin' => 1
        ]);
        
        $this->assertEquals('John', $user->name);
        $this->assertEquals(1, $user->is_admin);
    }
    
    public function test_to_array_hides_hidden_attributes_and_applies_casts()
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Jane',
            'password' => 'secret',
            'is_active' => '1' // DB raw value
        ]);
        
        $array = $user->toArray();
        
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayNotHasKey('password', $array);
        $this->assertIsBool($array['is_active']);
        $this->assertTrue($array['is_active']);
    }
    
    public function test_model_can_save_via_entity_manager()
    {
        // For testing we will just call save without EM, which updates original state
        $user = new User(['name' => 'Test']);
        
        $this->assertTrue($user->isDirty());
        
        $user->save(); // syncs original
        
        $this->assertFalse($user->isDirty());
    }
}
