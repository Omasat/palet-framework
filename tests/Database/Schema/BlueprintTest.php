<?php

declare(strict_types=1);

namespace Tests\Database\Schema;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Schema\Blueprint;
use Palet\Framework\Database\Schema\ColumnDefinition;

class BlueprintTest extends TestCase
{
    public function test_blueprint_adds_columns_fluently()
    {
        $blueprint = new Blueprint('users');
        
        $column = $blueprint->string('name')->nullable()->default('John');
        
        $this->assertInstanceOf(ColumnDefinition::class, $column);
        $this->assertEquals('name', $column->get('name'));
        $this->assertTrue($column->get('nullable'));
        $this->assertEquals('John', $column->get('default'));
        
        $this->assertCount(1, $blueprint->getColumns());
    }

    public function test_blueprint_registers_commands()
    {
        $blueprint = new Blueprint('users');
        $blueprint->create();
        $blueprint->drop();
        
        $commands = $blueprint->getCommands();
        
        $this->assertCount(2, $commands);
        $this->assertEquals('create', $commands[0]['name']);
        $this->assertEquals('drop', $commands[1]['name']);
    }
}
