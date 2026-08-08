<?php

declare(strict_types=1);

namespace Tests\Http\Message;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Uri;

class RequestTest extends TestCase
{
    public function test_immutability()
    {
        $request = new Request('GET', 'http://example.com');
        
        $new = $request->withMethod('POST')->withHeader('X-Test', '123');
        
        $this->assertEquals('GET', $request->getMethod());
        $this->assertFalse($request->hasHeader('X-Test'));
        
        $this->assertEquals('POST', $new->getMethod());
        $this->assertEquals('123', $new->getHeaderLine('X-Test'));
    }

    public function test_header_case_insensitivity()
    {
        $request = new Request();
        $request = $request->withHeader('Content-Type', 'application/json');
        
        $this->assertTrue($request->hasHeader('content-type'));
        $this->assertTrue($request->hasHeader('CONTENT-TYPE'));
        $this->assertEquals('application/json', $request->getHeaderLine('content-type'));
    }

    public function test_with_added_header()
    {
        $request = new Request();
        $request = $request->withHeader('Foo', 'Bar')
                           ->withAddedHeader('foo', 'Baz');
        
        $this->assertEquals('Bar,Baz', $request->getHeaderLine('foo'));
    }

    public function test_with_uri_updates_host_header()
    {
        $request = new Request();
        $request = $request->withUri(new Uri('http://test.com'));
        
        $this->assertEquals('test.com', $request->getHeaderLine('Host'));
    }
}
