<?php

declare(strict_types=1);

namespace Tests\Cache\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Cache\Drivers\RedisDriver;
use Redis;

class RedisDriverTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Redis')) {
            $this->markTestSkipped('Redis extension is not installed.');
        }
    }

    public function test_get_and_set()
    {
        $redis = $this->createMock(Redis::class);
        $redis->expects($this->once())->method('set')->with('foo', 'bar')->willReturn(true);
        $redis->expects($this->once())->method('get')->with('foo')->willReturn('bar');
        
        $driver = new RedisDriver($redis);
        
        $this->assertTrue($driver->set('foo', 'bar'));
        $this->assertEquals('bar', $driver->get('foo'));
    }
}
