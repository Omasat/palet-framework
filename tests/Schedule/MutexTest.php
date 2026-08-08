<?php

declare(strict_types=1);

namespace Tests\Schedule;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Schedule\Mutex;
use Palet\Framework\Schedule\Event;
use Palet\Framework\Schedule\CronExpression;
use Palet\Framework\Contracts\Cache\CacheStoreInterface;

class MutexTest extends TestCase
{
    public function test_mutex_create_returns_true_if_lock_acquired()
    {
        $cache = $this->createMock(CacheStoreInterface::class);
        $cache->expects($this->once())
              ->method('has')
              ->willReturn(false);
              
        $cache->expects($this->once())
              ->method('set')
              ->willReturn(true);
              
        $mutex = new Mutex($cache);
        
        $event = new Event($mutex, new CronExpression(), 'cmd');
        $time = new \DateTimeImmutable('2026-08-01 12:00:00');
        
        $this->assertTrue($mutex->create($event, $time));
    }

    public function test_mutex_create_returns_false_if_already_locked()
    {
        $cache = $this->createMock(CacheStoreInterface::class);
        $cache->expects($this->once())
              ->method('has')
              ->willReturn(true);
              
        $mutex = new Mutex($cache);
        
        $event = new Event($mutex, new CronExpression(), 'cmd');
        $time = new \DateTimeImmutable('2026-08-01 12:00:00');
        
        $this->assertFalse($mutex->create($event, $time));
    }
}
