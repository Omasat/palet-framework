<?php

declare(strict_types=1);

namespace Tests\Queue;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Queue\Drivers\SyncQueue;

class DummyJob implements \Palet\Framework\Contracts\Queue\JobInterface
{
    public static bool $executed = false;
    
    public function handle(): void
    {
        self::$executed = true;
    }
    public function getId(): string { return '1'; }
    public function getQueue(): string { return 'default'; }
    public function setQueue(string $queue): void {}
    public function getDelay(): int { return 0; }
    public function setDelay(int $delay): void {}
    public function getMaxTries(): int { return 1; }
    public function getAttempts(): int { return 1; }
    public function incrementAttempts(): void {}
    public function release(int $delay = 0): void {}
    public function markAsFailed(\Throwable $exception): void {}
}

class SyncQueueTest extends TestCase
{
    public function setUp(): void
    {
        DummyJob::$executed = false;
    }

    public function test_push_executes_job_synchronously()
    {
        $queue = new SyncQueue();
        
        $this->assertFalse(DummyJob::$executed);
        
        $queue->push(new DummyJob());
        
        $this->assertTrue(DummyJob::$executed);
    }
}
