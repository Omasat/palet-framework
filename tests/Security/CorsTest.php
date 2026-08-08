<?php

declare(strict_types=1);

namespace Tests\Security;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Security\Cors\CorsManager;
use Palet\Framework\Security\Middleware\HandleCorsMiddleware;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Response;
use Palet\Framework\Http\Response\ResponseFactory;
use Closure;

class CorsTest extends TestCase
{
    public function test_manager_adds_cors_headers_for_allowed_origin()
    {
        $manager = new CorsManager(['allowed_origins' => ['https://example.com']]);
        
        $request = (new Request('GET', '/'))->withHeader('Origin', 'https://example.com');
        $response = new Response();
        
        $result = $manager->handle($request, $response);
        
        $this->assertTrue($result->hasHeader('Access-Control-Allow-Origin'));
        $this->assertEquals('https://example.com', $result->getHeaderLine('Access-Control-Allow-Origin'));
    }

    public function test_manager_does_not_add_headers_for_disallowed_origin()
    {
        $manager = new CorsManager(['allowed_origins' => ['https://example.com']]);
        
        $request = (new Request('GET', '/'))->withHeader('Origin', 'https://malicious.com');
        $response = new Response();
        
        $result = $manager->handle($request, $response);
        
        $this->assertFalse($result->hasHeader('Access-Control-Allow-Origin'));
    }

    public function test_middleware_handles_preflight_request()
    {
        $manager = new CorsManager(['allowed_origins' => ['*']]);
        $middleware = new HandleCorsMiddleware($manager, new ResponseFactory());
        
        $request = (new Request('OPTIONS', '/'))
            ->withHeader('Origin', 'https://example.com')
            ->withHeader('Access-Control-Request-Method', 'POST');
            
        $response = $middleware->handle($request, function ($req) {
            return new Response();
        });
        
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('https://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('POST', $response->getHeaderLine('Access-Control-Allow-Methods'));
    }
}
