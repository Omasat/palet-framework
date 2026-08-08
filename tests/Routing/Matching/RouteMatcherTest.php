<?php

declare(strict_types=1);

namespace Tests\Routing\Matching;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Matching\RouteMatcher;
use Palet\Framework\Routing\RouteCollection;
use Palet\Framework\Routing\Route;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Routing\Exceptions\RouteNotFoundException;
use Palet\Framework\Routing\Exceptions\MethodNotAllowedException;

class RouteMatcherTest extends TestCase
{
    public function test_matches_route_successfully()
    {
        $matcher = new RouteMatcher();
        $collection = new RouteCollection();
        
        $route1 = new Route('GET', '/users', 'action1');
        $route2 = new Route('GET', '/users/{id}', 'action2');
        
        $collection->add($route1);
        $collection->add($route2);
        
        $request = new Request('GET', '/users/456');
        $match = $matcher->match($request, $collection);
        
        $this->assertSame($route2, $match->route);
        $this->assertEquals(['id' => '456'], $match->parameters);
    }

    public function test_throws_route_not_found_exception()
    {
        $matcher = new RouteMatcher();
        $collection = new RouteCollection();
        
        $collection->add(new Route('GET', '/users', 'action'));
        
        $request = new Request('GET', '/posts');
        
        $this->expectException(RouteNotFoundException::class);
        $matcher->match($request, $collection);
    }

    public function test_throws_method_not_allowed_exception()
    {
        $matcher = new RouteMatcher();
        $collection = new RouteCollection();
        
        $collection->add(new Route('POST', '/users', 'action'));
        $collection->add(new Route('PUT', '/users', 'action'));
        
        $request = new Request('GET', '/users');
        
        try {
            $matcher->match($request, $collection);
            $this->fail('Expected MethodNotAllowedException');
        } catch (MethodNotAllowedException $e) {
            $this->assertEquals(['POST', 'PUT'], $e->getAllowedMethods());
        }
    }
}
