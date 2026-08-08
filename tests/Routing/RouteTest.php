<?php

declare(strict_types=1);

namespace Tests\Routing;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Route;

class RouteTest extends TestCase
{
    public function test_route_initialization()
    {
        $route = new Route('GET', '/users', 'UserController@index');
        
        $this->assertEquals('/users', $route->getUri());
        $this->assertEquals(['GET', 'HEAD'], $route->getMethods());
        $this->assertEquals('UserController@index', $route->getAction());
    }

    public function test_route_prefix_formatting()
    {
        $route = new Route('GET', 'users', 'action');
        $route->prefix('api/v1/');
        
        $this->assertEquals('/api/v1/users', $route->getUri());
        
        $route2 = new Route('GET', '/', 'action');
        $route2->prefix('api');
        
        $this->assertEquals('/api', $route2->getUri());
    }

    public function test_route_name_concatenation()
    {
        $route = new Route('GET', '/', 'action');
        $route->name('admin.');
        $route->name('users.index');
        
        $this->assertEquals('admin.users.index', $route->getName());
    }
}
