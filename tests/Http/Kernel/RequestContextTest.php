<?php

declare(strict_types=1);

namespace Tests\Http\Kernel;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Kernel\RequestContext;
use Palet\Framework\Http\Message\Request;

class RequestContextTest extends TestCase
{
    public function test_creates_context_from_request()
    {
        $request = new Request('POST', '/api/users');
        $request = $request->withHeader('X-Trace-Id', 'trace-123')
                           ->withHeader('X-Forwarded-For', '192.168.1.1');
        
        // Mock server params inside request (or just assume default)
        
        $context = RequestContext::fromRequest($request);
        
        $this->assertNotEmpty($context->requestId);
        $this->assertEquals('trace-123', $context->traceId);
        $this->assertEquals('192.168.1.1', $context->clientIp);
        $this->assertEquals('POST', $context->method);
        $this->assertEquals('/api/users', $context->path);
    }
}
