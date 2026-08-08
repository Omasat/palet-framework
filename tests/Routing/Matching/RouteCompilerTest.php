<?php

declare(strict_types=1);

namespace Tests\Routing\Matching;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Matching\RouteCompiler;
use Palet\Framework\Routing\Route;

class RouteCompilerTest extends TestCase
{
    public function test_compiles_static_route()
    {
        $compiler = new RouteCompiler();
        $route = new Route('GET', '/users', 'action');
        
        $compiled = $compiler->compile($route);
        
        $this->assertEquals('#^/users$#su', $compiled->regex);
        $this->assertEmpty($compiled->variables);
    }

    public function test_compiles_dynamic_route()
    {
        $compiler = new RouteCompiler();
        $route = new Route('GET', '/users/{id}', 'action');
        
        $compiled = $compiler->compile($route);
        
        $this->assertEquals('#^/users/(?P<id>[a-zA-Z0-9_-]+)$#su', $compiled->regex);
        $this->assertEquals(['id'], $compiled->variables);
    }

    public function test_compiles_optional_parameter()
    {
        $compiler = new RouteCompiler();
        $route = new Route('GET', '/users/{id?}', 'action');
        
        $compiled = $compiler->compile($route);
        
        $this->assertEquals('#^/users(?:/(?P<id>[a-zA-Z0-9_-]+))?$#su', $compiled->regex);
        $this->assertEquals(['id'], $compiled->variables);
    }

    public function test_compiles_route_with_where_constraints()
    {
        $compiler = new RouteCompiler();
        $route = new Route('GET', '/users/{id}', 'action');
        $route->where('id', '[0-9]+');
        
        $compiled = $compiler->compile($route);
        
        $this->assertEquals('#^/users/(?P<id>[0-9]+)$#su', $compiled->regex);
    }
}
