<?php

declare(strict_types=1);

namespace Tests\Routing\Dispatcher;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Dispatcher\ActionResolver;
use InvalidArgumentException;

class ActionResolverTest extends TestCase
{
    public function test_resolves_string_action()
    {
        $resolver = new ActionResolver();
        
        $meta1 = $resolver->resolve('UserController@index');
        $this->assertFalse($meta1->isClosure);
        $this->assertEquals('UserController', $meta1->controllerClass);
        $this->assertEquals('index', $meta1->method);
        
        $meta2 = $resolver->resolve('InvokableController');
        $this->assertFalse($meta2->isClosure);
        $this->assertEquals('InvokableController', $meta2->controllerClass);
        $this->assertEquals('__invoke', $meta2->method);
    }

    public function test_resolves_array_action()
    {
        $resolver = new ActionResolver();
        
        $meta = $resolver->resolve(['UserController', 'index']);
        
        $this->assertFalse($meta->isClosure);
        $this->assertEquals('UserController', $meta->controllerClass);
        $this->assertEquals('index', $meta->method);
    }

    public function test_resolves_closure_action()
    {
        $resolver = new ActionResolver();
        
        $action = function () {};
        $meta = $resolver->resolve($action);
        
        $this->assertTrue($meta->isClosure);
        $this->assertSame($action, $meta->closure);
    }

    public function test_throws_exception_for_invalid_action()
    {
        $resolver = new ActionResolver();
        
        $this->expectException(InvalidArgumentException::class);
        $resolver->resolve(123);
    }
}
