<?php

declare(strict_types=1);

namespace Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Middleware\MiddlewareDispatcher;
use Palet\Framework\Http\Middleware\MiddlewareRegistry;
use Palet\Framework\Http\Middleware\MiddlewareResolver;
use Palet\Framework\Http\Middleware\MiddlewarePriorityResolver;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Response;
use Palet\Framework\Routing\Route;
use Closure;

class MiddlewareDispatcherTest extends TestCase
{
    public function test_dispatches_middleware_pipeline()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $app->method('make')->willReturnCallback(function ($class) {
            return new $class();
        });
        
        $registry = new MiddlewareRegistry();
        $registry->pushGlobal(DummyGlobalMiddleware::class);
        $registry->alias('route_mid', DummyRouteMiddleware::class);
        
        $resolver = new MiddlewareResolver($registry);
        $priorityResolver = new MiddlewarePriorityResolver();
        
        $dispatcher = new MiddlewareDispatcher($app, $registry, $resolver, $priorityResolver);
        
        $request = new Request();
        $route = new Route('GET', '/', 'action');
        $route->middleware('route_mid');
        
        $destination = function ($req) {
            return new Response(200, [], 'Action');
        };
        
        $response = $dispatcher->dispatch($request, $route, $destination);
        
        $this->assertEquals('Action', $response->getBody()->getContents());
        $this->assertEquals('Global', $response->getHeaderLine('X-Global'));
        $this->assertEquals('Route', $response->getHeaderLine('X-Route'));
    }
}

class DummyGlobalMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response->withHeader('X-Global', 'Global');
    }
}

class DummyRouteMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response->withHeader('X-Route', 'Route');
    }
}
