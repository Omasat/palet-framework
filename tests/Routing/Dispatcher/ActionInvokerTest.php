<?php

declare(strict_types=1);

namespace Tests\Routing\Dispatcher;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Dispatcher\ActionInvoker;
use Palet\Framework\Http\Message\Request;
use BadMethodCallException;

class ActionInvokerTest extends TestCase
{
    public function test_invokes_closure_with_parameters()
    {
        $invoker = new ActionInvoker();
        $request = new Request();
        
        $action = function ($id, $name = 'guest') {
            return $id . '-' . $name;
        };
        
        $result = $invoker->invoke($action, $request, ['id' => 123]);
        
        $this->assertEquals('123-guest', $result);
    }

    public function test_invokes_controller_method()
    {
        $invoker = new ActionInvoker();
        $request = new Request();
        
        $controller = new InvokableDummyController();
        
        $result = $invoker->invoke([$controller, 'index'], $request, ['id' => 456]);
        
        $this->assertEquals('Index: 456', $result);
    }

    public function test_prevents_private_method_invocation()
    {
        $invoker = new ActionInvoker();
        $request = new Request();
        
        $controller = new InvokableDummyController();
        
        $this->expectException(BadMethodCallException::class);
        $invoker->invoke([$controller, 'secret'], $request, []);
    }
}

class InvokableDummyController
{
    public function index($id)
    {
        return 'Index: ' . $id;
    }
    
    private function secret()
    {
        return 'Secret';
    }
}
