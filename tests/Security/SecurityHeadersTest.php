<?php

declare(strict_types=1);

namespace Tests\Security;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Security\Http\SecurityHeadersManager;
use Palet\Framework\Security\Middleware\SecurityHeadersMiddleware;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Response;
use Closure;

class SecurityHeadersTest extends TestCase
{
    public function test_manager_applies_default_headers()
    {
        $manager = new SecurityHeadersManager();
        $response = new Response();
        
        $result = $manager->apply($response);
        
        $this->assertEquals('SAMEORIGIN', $result->getHeaderLine('X-Frame-Options'));
        $this->assertEquals('nosniff', $result->getHeaderLine('X-Content-Type-Options'));
        $this->assertEquals('1; mode=block', $result->getHeaderLine('X-XSS-Protection'));
    }

    public function test_middleware_applies_headers()
    {
        $manager = new SecurityHeadersManager();
        $middleware = new SecurityHeadersMiddleware($manager);
        
        $request = new Request('GET', '/');
        $response = $middleware->handle($request, function ($req) {
            return new Response();
        });
        
        $this->assertTrue($response->hasHeader('X-Frame-Options'));
        $this->assertTrue($response->hasHeader('Content-Security-Policy'));
    }
}
