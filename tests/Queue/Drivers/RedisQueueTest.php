<?php

declare(strict_types=1);

namespace Tests\Queue\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Queue\Drivers\RedisQueue;
use Palet\Framework\Contracts\Queue\JobInterface;
use Redis;

class RedisQueueTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Redis')) {
            $this->markTestSkipped('Redis extension is not installed.');
        }
    }

    public function test_push_and_pop()
    {
        $redis = $this->createMock(Redis::class);
        $job = $this->createMock(JobInterface::class);
        
        $redis->expects($this->once())->method('rpush')->willReturn(1);
        $redis->method('lpop')->willReturn(json_encode([
            'job' => serialize($job),
            'queue' => 'default',
            'attempts' => 0,
            'id' => '123'
        ]));
        
        $queue = new RedisQueue($redis);
        
        $queue->push($job);
        
        $popped = $queue->pop();
        $this->assertInstanceOf(JobInterface::class, $popped);
    }
}
