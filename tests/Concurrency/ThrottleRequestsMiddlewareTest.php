<?php

declare(strict_types=1);

namespace Tests\Concurrency;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Concurrency\Middleware\ThrottleRequestsMiddleware;
use Palet\Framework\Concurrency\RateLimiter\RateLimiter;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Response;
use Palet\Framework\Http\Response\ResponseFactory;

class ThrottleRequestsMiddlewareTest extends TestCase
{
    protected RateLimiter $limiter;
    protected ThrottleRequestsMiddleware $middleware;

    protected function setUp(): void
    {
        $cache = new ArrayCacheStore();
        $this->limiter = new RateLimiter($cache);
        $this->middleware = new ThrottleRequestsMiddleware($this->limiter, new ResponseFactory());
    }

    public function test_middleware_allows_requests_within_limit()
    {
        $request = (new Request('GET', '/'))->withHeader('REMOTE_ADDR', '192.168.1.1');
        
        $response = $this->middleware->handle($request, function ($req) {
            return new Response();
        });
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('X-RateLimit-Remaining'));
        $this->assertEquals('59', $response->getHeaderLine('X-RateLimit-Remaining'));
    }

    public function test_middleware_blocks_requests_over_limit()
    {
        $request = (new Request('GET', '/'))->withHeader('REMOTE_ADDR', '192.168.1.2');
        
        // Simulate 60 hits (the default max limit)
        for ($i = 0; $i < 60; $i++) {
            $this->middleware->handle($request, function ($req) {
                return new Response();
            });
        }
        
        // The 61st hit should be blocked
        $response = $this->middleware->handle($request, function ($req) {
            return new Response(); // Should not reach here
        });
        
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertEquals('0', $response->getHeaderLine('X-RateLimit-Remaining'));
        $this->assertTrue($response->hasHeader('Retry-After'));
    }
}
