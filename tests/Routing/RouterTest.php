<?php

declare(strict_types=1);

namespace Tests\Routing;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Router;
use InvalidArgumentException;

class RouterTest extends TestCase
{
    public function test_registers_basic_routes()
    {
        $router = new Router();
        
        $router->get('/get', 'action');
        $router->post('/post', 'action');
        
        $routes = $router->getRoutes()->getRoutes();
        $this->assertCount(2, $routes);
        
        $this->assertEquals('/get', $routes[0]->getUri());
        $this->assertEquals(['GET', 'HEAD'], $routes[0]->getMethods());
    }

    public function test_registers_grouped_routes()
    {
        $router = new Router();
        
        $router->prefix('api')->middleware('auth')->name('api.')->group(function ($router) {
            $router->get('/users', 'action')->name('users.index');
        });
        
        $route = $router->getRoutes()->getRoutes()[0];
        
        $this->assertEquals('/api/users', $route->getUri());
        $this->assertEquals('api.users.index', $route->getName());
        // Middleware is not directly accessible via getter yet, but it's merged.
    }

    public function test_nested_groups()
    {
        $router = new Router();
        
        $router->prefix('api')->group(function ($router) {
            $router->prefix('v1')->group(function ($router) {
                $router->get('/status', 'action');
            });
        });
        
        $route = $router->getRoutes()->getRoutes()[0];
        $this->assertEquals('/api/v1/status', $route->getUri());
    }

    public function test_invalid_match_method_throws_exception()
    {
        $router = new Router();
        
        $this->expectException(InvalidArgumentException::class);
        $router->match(['GET', 'FOO'], '/test', 'action');
    }
}
