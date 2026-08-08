<?php

declare(strict_types=1);

namespace Tests\Database\Factories;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => clone $this->faker ? 'test@test.com' : 'test@test.com', // fake isolation
            'role' => 'user',
            'is_active' => false,
        ];
    }
    
    public function admin(): array
    {
        return [
            'role' => 'admin',
        ];
    }
    
    public function active(): array
    {
        return [
            'is_active' => true,
        ];
    }
}

class FactoryBuilderTest extends TestCase
{
    public function test_factory_can_make_multiple_records()
    {
        $records = UserFactory::new()->count(3)->make();
        
        $this->assertCount(3, $records);
        $this->assertArrayHasKey('name', $records[0]);
        $this->assertEquals('user', $records[0]['role']);
    }

    public function test_factory_applies_states()
    {
        $records = UserFactory::new()->state('admin')->state('active')->make();
        
        $this->assertCount(1, $records);
        $this->assertEquals('admin', $records[0]['role']);
        $this->assertTrue($records[0]['is_active']);
    }
    
    public function test_factory_applies_sequences()
    {
        $records = UserFactory::new()
            ->count(4)
            ->sequence(
                ['role' => 'admin'],
                ['role' => 'editor']
            )
            ->make();
            
        $this->assertEquals('admin', $records[0]['role']);
        $this->assertEquals('editor', $records[1]['role']);
        $this->assertEquals('admin', $records[2]['role']);
        $this->assertEquals('editor', $records[3]['role']);
    }

    public function test_factory_triggers_after_make_hooks()
    {
        $records = UserFactory::new()
            ->afterMake(function(&$attributes) {
                $attributes['hook_triggered'] = true;
            })
            ->make();
            
        $this->assertTrue($records[0]['hook_triggered']);
    }
}
