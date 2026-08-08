<?php

declare(strict_types=1);

namespace Tests\Routing;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\RouteCollection;
use Palet\Framework\Routing\Route;
use LogicException;

class RouteCollectionTest extends TestCase
{
    public function test_adds_and_retrieves_routes()
    {
        $collection = new RouteCollection();
        $route = new Route('GET', '/test', 'action');
        $route->name('test.route');
        
        $collection->add($route);
        
        $this->assertCount(1, $collection->getRoutes());
        $this->assertSame($route, $collection->getByName('test.route'));
        $this->assertArrayHasKey('/test', $collection->getRoutesByMethod()['GET']);
    }

    public function test_throws_exception_on_duplicate_name()
    {
        $collection = new RouteCollection();
        
        $route1 = new Route('GET', '/test1', 'action');
        $route1->name('test.name');
        
        $route2 = new Route('GET', '/test2', 'action');
        $route2->name('test.name');
        
        $collection->add($route1);
        $collection->add($route2);
        
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Route named [test.name] has already been defined.');
        
        $collection->refreshNameLookups();
    }
}
