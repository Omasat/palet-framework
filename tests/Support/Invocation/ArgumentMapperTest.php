<?php

declare(strict_types=1);

namespace Tests\Support\Invocation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Invocation\ArgumentMapper;
use Palet\Framework\Support\Invocation\ParameterResolver;
use Palet\Framework\Support\Invocation\DependencyResolver;
use Palet\Framework\Support\Invocation\InvocationContext;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use ReflectionFunction;

class ArgumentMapperTest extends TestCase
{
    public function test_maps_arguments_correctly()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $depResolver = new DependencyResolver($app);
        $paramResolver = new ParameterResolver($depResolver);
        
        $mapper = new ArgumentMapper($paramResolver);
        
        $action = function ($id, ?string $search, $page = 1) {};
        $ref = new ReflectionFunction($action);
        
        $context = new InvocationContext(['id' => 456, 'search' => 'test']);
        
        $args = $mapper->map($ref->getParameters(), $context);
        
        $this->assertEquals([456, 'test', 1], $args);
    }
}
