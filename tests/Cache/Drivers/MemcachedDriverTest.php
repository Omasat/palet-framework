<?php

declare(strict_types=1);

namespace Tests\Cache\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Cache\Drivers\MemcachedDriver;
use Memcached;

class MemcachedDriverTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Memcached')) {
            $this->markTestSkipped('Memcached extension is not installed.');
        }
    }

    public function test_get_and_set()
    {
        $memcached = $this->createMock(Memcached::class);
        $memcached->expects($this->once())->method('set')->with('foo', 'bar', 0)->willReturn(true);
        $memcached->expects($this->once())->method('get')->with('foo')->willReturn('bar');
        
        $driver = new MemcachedDriver($memcached);
        
        $this->assertTrue($driver->set('foo', 'bar'));
        $this->assertEquals('bar', $driver->get('foo'));
    }
}
