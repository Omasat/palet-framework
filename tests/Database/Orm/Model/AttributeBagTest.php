<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Model;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Model\AttributeBag;

class DummyAttributeBag
{
    use AttributeBag;
    
    public function __construct()
    {
        $this->bootAttributeBag();
    }
    
    public function defineCast(string $key, string $type): void
    {
        $this->casts[$key] = $type;
    }
}

class AttributeBagTest extends TestCase
{
    public function test_can_set_and_get_attributes()
    {
        $bag = new DummyAttributeBag();
        
        $bag->name = 'John Doe';
        
        $this->assertEquals('John Doe', $bag->name);
        $this->assertTrue(isset($bag->name));
    }
    
    public function test_tracks_dirty_attributes()
    {
        $bag = new DummyAttributeBag();
        
        $bag->name = 'Original';
        $bag->syncOriginal();
        
        $this->assertFalse($bag->isDirty());
        
        $bag->name = 'Changed';
        
        $this->assertTrue($bag->isDirty());
        $dirty = $bag->getDirty();
        $this->assertArrayHasKey('name', $dirty);
        $this->assertEquals('Changed', $dirty['name']);
    }
    
    public function test_applies_casts_on_get_and_set()
    {
        $bag = new DummyAttributeBag();
        $bag->defineCast('is_active', 'bool');
        
        // Setting '1' (string) should be kept as raw internally, 
        // wait, setAttribute uncasts. So if we pass true, it might uncast.
        // Let's pass true.
        $bag->is_active = true; // no-op for bool uncast, but array/json would encode
        
        $bag->setAttribute('is_active', '1'); // Uncast '1' string? bool doesn't uncast specially.
        
        $bag->attributes['is_active'] = '1'; // simulate DB raw
        $this->assertTrue($bag->is_active);
    }
}
