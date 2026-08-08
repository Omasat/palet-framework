<?php

declare(strict_types=1);

namespace Tests\Cookie;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Cookie\CookieJar;
use Palet\Framework\Cookie\Cookie;

class CookieJarTest extends TestCase
{
    public function test_make_returns_cookie_instance()
    {
        $jar = new CookieJar();
        $cookie = $jar->make('test_cookie', 'test_value', 10);
        
        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertEquals('test_cookie', $cookie->getName());
        $this->assertEquals('test_value', $cookie->getValue());
    }

    public function test_queue_and_has_queued()
    {
        $jar = new CookieJar();
        $jar->queue('queued_cookie', 'value', 10);
        
        $this->assertTrue($jar->hasQueued('queued_cookie'));
        $this->assertInstanceOf(Cookie::class, $jar->queued('queued_cookie'));
    }

    public function test_cookie_to_string()
    {
        $cookie = new Cookie('name', 'value', 0, '/', '', false, true, false, 'Lax');
        $str = (string) $cookie;
        
        $this->assertStringContainsString('name=value', $str);
        $this->assertStringContainsString('path=/', $str);
        $this->assertStringContainsString('httponly', $str);
        $this->assertStringContainsString('samesite=Lax', $str);
    }
}
