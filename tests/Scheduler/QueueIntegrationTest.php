<?php

declare(strict_types=1);

namespace Tests\Scheduler;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Scheduler\Tasks\QueueTask;
use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;

class QueueIntegrationTest extends TestCase
{
    public function test_queue_task_pushes_to_queue()
    {
        $queuePushed = false;
        
        $queueMock = new class($queuePushed) implements QueueInterface {
            public function __construct(public bool &$pushed) {}
            public function push(JobInterface $job, string $queue = 'default'): void { $this->pushed = true; }
            public function pushOn(string $queue, JobInterface $job): void {}
            public function pushDelayed(JobInterface $job, int $delay, string $queue = 'default'): void {}
        };
        
        $jobMock = new class implements JobInterface {
            public function handle(): void {}
            public function getId(): string { return '1'; }
            public function getQueue(): string { return 'default'; }
            public function setQueue(string $queue): void {}
            public function getDelay(): int { return 0; }
            public function setDelay(int $delay): void {}
            public function getMaxTries(): int { return 3; }
            public function getAttempts(): int { return 0; }
            public function incrementAttempts(): void {}
            public function release(int $delay = 0): void {}
            public function markAsFailed(\Throwable $exception): void {}
        };

        $task = new QueueTask($queueMock, $jobMock);
        $task->run();
        
        $this->assertTrue($queuePushed);
    }
}
