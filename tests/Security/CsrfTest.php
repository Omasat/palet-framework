<?php

declare(strict_types=1);

namespace Tests\Security;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Security\Csrf\CsrfTokenManager;
use Palet\Framework\Security\Csrf\TokenMismatchException;
use Palet\Framework\Security\Middleware\VerifyCsrfTokenMiddleware;
use Palet\Framework\Session\Store;
use Palet\Framework\Session\Drivers\ArraySessionDriver;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Response;
use Closure;

class CsrfTest extends TestCase
{
    protected CsrfTokenManager $manager;
    protected Store $session;

    protected function setUp(): void
    {
        $this->session = new Store('test', new ArraySessionDriver(10));
        $this->session->start();
        $this->session->regenerateToken();

        $this->manager = new CsrfTokenManager();
        $this->manager->setSession($this->session);
    }

    public function test_token_generation_and_validation()
    {
        $token = $this->manager->token();
        
        $this->assertTrue(is_string($token));
        $this->assertTrue($this->manager->validate($token));
        $this->assertFalse($this->manager->validate('invalid_token'));
    }

    public function test_middleware_allows_read_requests()
    {
        $middleware = new VerifyCsrfTokenMiddleware($this->manager);
        
        $request = new Request('GET', '/');
        $response = $middleware->handle($request, function ($req) {
            return new Response();
        });
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_middleware_blocks_post_request_without_token()
    {
        $middleware = new VerifyCsrfTokenMiddleware($this->manager);
        $request = new Request('POST', '/');
        
        $this->expectException(TokenMismatchException::class);
        $middleware->handle($request, function ($req) {
            return new Response();
        });
    }

    public function test_middleware_allows_post_request_with_valid_header_token()
    {
        $middleware = new VerifyCsrfTokenMiddleware($this->manager);
        $token = $this->manager->token();
        
        $request = (new Request('POST', '/'))->withHeader('X-CSRF-TOKEN', $token);
        $response = $middleware->handle($request, function ($req) {
            return new Response();
        });
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
