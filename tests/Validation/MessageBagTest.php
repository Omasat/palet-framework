<?php

declare(strict_types=1);

namespace Tests\Validation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Validation\MessageBag;

class MessageBagTest extends TestCase
{
    public function test_can_add_and_retrieve_messages()
    {
        $bag = new MessageBag();
        
        $this->assertFalse($bag->has('email'));
        
        $bag->add('email', 'Invalid email');
        $bag->add('email', 'Email is required');
        
        $this->assertTrue($bag->has('email'));
        
        $this->assertEquals('Invalid email', $bag->first('email'));
        
        $this->assertCount(2, $bag->get('email'));
        
        $this->assertIsArray($bag->toArray());
        $this->assertArrayHasKey('email', $bag->toArray());
    }
    
    public function test_constructor_accepts_initial_messages()
    {
        $bag = new MessageBag([
            'name' => 'Name is required'
        ]);
        
        $this->assertTrue($bag->has('name'));
        $this->assertEquals('Name is required', $bag->first('name'));
    }
}
