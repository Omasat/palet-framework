<?php

declare(strict_types=1);

namespace Tests\Support\Invocation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Invocation\ParameterResolver;
use Palet\Framework\Support\Invocation\DependencyResolver;
use Palet\Framework\Support\Invocation\InvocationContext;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use RuntimeException;
use ReflectionFunction;

class ParameterResolverTest extends TestCase
{
    public function test_resolves_from_context()
    {
        $depResolver = $this->createMock(DependencyResolver::class);
        $resolver = new ParameterResolver($depResolver);
        
        $action = function ($id) {};
        $ref = new ReflectionFunction($action);
        $param = $ref->getParameters()[0];
        
        $context = new InvocationContext(['id' => 999]);
        
        $this->assertEquals(999, $resolver->resolve($param, $context));
    }

    public function test_resolves_from_container()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $depResolver = new DependencyResolver($app);
        
        $app->method('has')->willReturnMap([[\stdClass::class, true]]);
        $app->method('make')->willReturn(new \stdClass());
        
        $resolver = new ParameterResolver($depResolver);
        
        $action = function (\stdClass $obj) {};
        $ref = new ReflectionFunction($action);
        $param = $ref->getParameters()[0];
        
        $context = new InvocationContext();
        
        $this->assertInstanceOf(\stdClass::class, $resolver->resolve($param, $context));
    }

    public function test_resolves_default_value()
    {
        $depResolver = $this->createMock(DependencyResolver::class);
        $resolver = new ParameterResolver($depResolver);
        
        $action = function ($page = 1) {};
        $ref = new ReflectionFunction($action);
        $param = $ref->getParameters()[0];
        
        $context = new InvocationContext();
        
        $this->assertEquals(1, $resolver->resolve($param, $context));
    }

    public function test_resolves_nullable()
    {
        $depResolver = $this->createMock(DependencyResolver::class);
        $resolver = new ParameterResolver($depResolver);
        
        $action = function (?string $search) {};
        $ref = new ReflectionFunction($action);
        $param = $ref->getParameters()[0];
        
        $context = new InvocationContext();
        
        $this->assertNull($resolver->resolve($param, $context));
    }

    public function test_throws_exception_if_unresolvable()
    {
        $depResolver = $this->createMock(DependencyResolver::class);
        $resolver = new ParameterResolver($depResolver);
        
        $action = function (int $id) {}; // No default, not in context, not in container
        $ref = new ReflectionFunction($action);
        $param = $ref->getParameters()[0];
        
        $context = new InvocationContext();
        
        $this->expectException(RuntimeException::class);
        $resolver->resolve($param, $context);
    }
}
