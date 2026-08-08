<?php

declare(strict_types=1);

namespace Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Middleware\MiddlewareTerminator;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Http\Middleware\TerminableMiddlewareInterface;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Response;
use Closure;

class MiddlewareTerminatorTest extends TestCase
{
    public function test_terminates_middleware()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $middleware = new DummyTerminableMiddleware();
        
        $app->method('make')->willReturn($middleware);
        
        $terminator = new MiddlewareTerminator($app);
        
        $request = new Request();
        $response = new Response();
        
        $terminator->terminate([DummyTerminableMiddleware::class], $request, $response);
        
        $this->assertTrue($middleware->terminated);
    }
}

class DummyTerminableMiddleware implements TerminableMiddlewareInterface
{
    public bool $terminated = false;
    
    public function handle($request, Closure $next): \Palet\Framework\Contracts\Http\Message\ResponseInterface
    {
        return $next($request);
    }

    public function terminate($request, $response): void
    {
        $this->terminated = true;
    }
}
