<?php

declare(strict_types=1);

namespace Tests\Routing\Dispatcher;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Dispatcher\ControllerDispatcher;
use Palet\Framework\Routing\Dispatcher\ActionResolver;
use Palet\Framework\Routing\Dispatcher\ControllerResolver;
use Palet\Framework\Routing\Dispatcher\ActionInvoker;
use Palet\Framework\Routing\Dispatcher\ActionResultNormalizer;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Routing\Matching\RouteMatch;
use Palet\Framework\Routing\Route;
use Palet\Framework\Http\Message\Request;

class ControllerDispatcherTest extends TestCase
{
    public function test_dispatches_request_to_action()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $actionResolver = new ActionResolver();
        $controllerResolver = new ControllerResolver($app);
        
        $methodInvoker = $this->createMock(\Palet\Framework\Contracts\Support\Invocation\MethodInvokerInterface::class);
        $methodInvoker->method('invoke')->willReturn(['user_id' => 99]);
        
        $normalizer = new ActionResultNormalizer();
        
        $dispatcher = new ControllerDispatcher(
            $actionResolver,
            $controllerResolver,
            $methodInvoker,
            $normalizer
        );
        
        $route = new Route('GET', '/users/{id}', function ($id) {
            return ['user_id' => $id];
        });
        
        $match = new RouteMatch($route, ['id' => 99]);
        $request = new Request();
        
        $response = $dispatcher->dispatch($request, $match);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"user_id":99}', $response->getBody()->getContents());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }
}
