<?php

declare(strict_types=1);

namespace Tests\Routing\Dispatcher;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Dispatcher\ControllerResolver;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use RuntimeException;

class ControllerResolverTest extends TestCase
{
    public function test_resolves_controller_via_container()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $app->method('make')->willReturn(new DummyController());
        
        $resolver = new ControllerResolver($app);
        
        $instance = $resolver->resolve(DummyController::class);
        $this->assertInstanceOf(DummyController::class, $instance);
    }

    public function test_throws_exception_if_class_not_found()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $resolver = new ControllerResolver($app);
        
        $this->expectException(RuntimeException::class);
        $resolver->resolve('NonExistentController');
    }
}

class DummyController {}
