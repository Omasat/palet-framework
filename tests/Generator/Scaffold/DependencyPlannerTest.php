<?php

declare(strict_types=1);

namespace Tests\Generator\Scaffold;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Scaffold\DependencyPlanner;
use RuntimeException;

class DependencyPlannerTest extends TestCase
{
    public function test_plans_dependencies_correctly()
    {
        $planner = new DependencyPlanner();
        
        $steps = ['module', 'entity', 'migration', 'repository'];
        $dependencies = [
            'entity' => ['module'],
            'migration' => ['entity'],
            'repository' => ['entity']
        ];
        
        $order = $planner->plan($steps, $dependencies);
        
        // Expected order should respect dependencies
        $modulePos = array_search('module', $order);
        $entityPos = array_search('entity', $order);
        $migrationPos = array_search('migration', $order);
        $repositoryPos = array_search('repository', $order);
        
        $this->assertLessThan($entityPos, $modulePos);
        $this->assertLessThan($migrationPos, $entityPos);
        $this->assertLessThan($repositoryPos, $entityPos);
    }

    public function test_throws_on_deep_circular_dependency()
    {
        $planner = new DependencyPlanner();
        
        $steps = ['a', 'b', 'c'];
        $dependencies = [
            'a' => ['c'],
            'b' => ['a'],
            'c' => ['b']
        ];
        
        $this->expectException(RuntimeException::class);
        $planner->plan($steps, $dependencies);
    }
}
