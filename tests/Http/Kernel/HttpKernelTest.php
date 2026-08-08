<?php

declare(strict_types=1);

namespace Tests\Http\Kernel;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Routing\RouterInterface;
use Palet\Framework\Foundation\Application;
use Palet\Framework\Http\Kernel\HttpKernel;
use Palet\Framework\Http\Message\Request;

class HttpKernelTest extends TestCase
{
    public function test_handles_request_and_returns_response()
    {
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $this->markTestSkipped('Test requires PHP 8.2+');
        }

        $app = $this->createMock(ApplicationInterface::class);
        $kernel = new HttpKernel($app);
        
        $request = new Request('GET', '/test');
        $response = $kernel->handle($request);
        
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found (Router Stub)', $response->getBody()->getContents());
    }

    public function test_catches_exceptions_and_returns_500()
    {
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $this->markTestSkipped('Test requires PHP 8.2+');
        }

        $app = $this->createMock(ApplicationInterface::class);
        $app->method('instance')->willThrowException(new \RuntimeException('Test Error'));
        
        $kernel = new HttpKernel($app);
        $request = new Request();
        
        $response = $kernel->handle($request);
        
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Test Error', $response->getBody()->getContents());
    }

    public function test_bootstrap_registers_router_for_real_application()
    {
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $this->markTestSkipped('Test requires PHP 8.2+');
        }

        $app = new Application(__DIR__);
        $kernel = new HttpKernel($app);
        $request = new Request('GET', '/test');

        $response = $kernel->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($app->has(RouterInterface::class));
    }
}
