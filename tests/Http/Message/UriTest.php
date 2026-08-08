<?php

declare(strict_types=1);

namespace Tests\Http\Message;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Message\Uri;

class UriTest extends TestCase
{
    public function test_parses_uri_and_returns_parts()
    {
        $uri = new Uri('https://user:pass@example.com:8080/path?query=1#fragment');
        
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/path', $uri->getPath());
        $this->assertEquals('query=1', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
        $this->assertEquals('user:pass@example.com:8080', $uri->getAuthority());
        $this->assertEquals('https://user:pass@example.com:8080/path?query=1#fragment', (string) $uri);
    }

    public function test_immutability()
    {
        $uri = new Uri('http://example.com');
        $newUri = $uri->withScheme('https')->withHost('test.com');
        
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('example.com', $uri->getHost());
        
        $this->assertEquals('https', $newUri->getScheme());
        $this->assertEquals('test.com', $newUri->getHost());
    }
}
