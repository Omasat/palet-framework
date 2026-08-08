<?php

declare(strict_types=1);

namespace Tests\Container;

use Palet\Framework\Container\Container;
use Palet\Framework\Container\Exception\BindingResolutionException;
use Palet\Framework\Container\Exception\CircularDependencyException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_closure_resolution()
    {
        $container = new Container();
        $container->bind('name', function () {
            return 'Palet';
        });
        
        $this->assertEquals('Palet', $container->make('name'));
    }

    public function test_singleton_resolution()
    {
        $container = new Container();
        $container->singleton('random', function () {
            return new \stdClass();
        });
        
        $instance1 = $container->make('random');
        $instance2 = $container->make('random');
        
        $this->assertSame($instance1, $instance2);
    }

    public function test_auto_wiring()
    {
        $container = new Container();
        $instance = $container->make(AutoWireTestClass::class);
        
        $this->assertInstanceOf(AutoWireTestClass::class, $instance);
        $this->assertInstanceOf(AutoWireDependency::class, $instance->dependency);
    }
    
    public function test_alias_resolution()
    {
        $container = new Container();
        $container->bind(AutoWireDependency::class, AutoWireDependency::class);
        $container->alias(AutoWireDependency::class, 'dep');
        
        $this->assertInstanceOf(AutoWireDependency::class, $container->make('dep'));
    }

    public function test_circular_dependency_exception()
    {
        $this->expectException(CircularDependencyException::class);
        
        $container = new Container();
        $container->make(CircularA::class);
    }

    public function test_contextual_binding()
    {
        $container = new Container();
        
        $container->when(ContextualA::class)
                  ->needs(ContextualInterface::class)
                  ->give(ContextualImplementationA::class);
                  
        $container->when(ContextualB::class)
                  ->needs(ContextualInterface::class)
                  ->give(ContextualImplementationB::class);
                  
        $a = $container->make(ContextualA::class);
        $b = $container->make(ContextualB::class);
        
        $this->assertInstanceOf(ContextualImplementationA::class, $a->dep);
        $this->assertInstanceOf(ContextualImplementationB::class, $b->dep);
    }
    
    public function test_contextual_binding_primitive()
    {
        $container = new Container();
        
        $container->when(ContextualPrimitive::class)
                  ->needs('$name')
                  ->give('PaletFramework');
                  
        $instance = $container->make(ContextualPrimitive::class);
        $this->assertEquals('PaletFramework', $instance->name);
    }

    public function test_unresolvable_primitive_exception()
    {
        $this->expectException(BindingResolutionException::class);
        
        $container = new Container();
        $container->make(ContextualPrimitive::class); // no contextual binding provided
    }
}

class AutoWireDependency {}
class AutoWireTestClass {
    public function __construct(public AutoWireDependency $dependency) {}
}

class CircularA {
    public function __construct(public CircularB $b) {}
}
class CircularB {
    public function __construct(public CircularA $a) {}
}

interface ContextualInterface {}
class ContextualImplementationA implements ContextualInterface {}
class ContextualImplementationB implements ContextualInterface {}
class ContextualA {
    public function __construct(public ContextualInterface $dep) {}
}
class ContextualB {
    public function __construct(public ContextualInterface $dep) {}
}
class ContextualPrimitive {
    public function __construct(public string $name) {}
}
