<?php

declare(strict_types=1);

namespace Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Authorization\Response;
use Palet\Framework\Authorization\AuthorizationException;

class ResponseTest extends TestCase
{
    public function test_allow_response()
    {
        $response = Response::allow('Access granted', 200);
        
        $this->assertTrue($response->allowed());
        $this->assertFalse($response->denied());
        $this->assertEquals('Access granted', $response->message());
        $this->assertEquals(200, $response->code());
    }

    public function test_deny_response()
    {
        $response = Response::deny('Access denied', 403);
        
        $this->assertFalse($response->allowed());
        $this->assertTrue($response->denied());
        $this->assertEquals('Access denied', $response->message());
        $this->assertEquals(403, $response->code());
    }

    public function test_authorize_throws_exception_on_deny()
    {
        $response = Response::deny('Forbidden', 403);
        
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('Forbidden');
        $this->expectExceptionCode(403);
        
        $response->authorize();
    }

    public function test_authorize_returns_self_on_allow()
    {
        $response = Response::allow('OK');
        
        $this->assertSame($response, $response->authorize());
    }
}
