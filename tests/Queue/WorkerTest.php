<?php

declare(strict_types=1);

namespace Tests\Queue;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Queue\Worker;
use Palet\Framework\Queue\QueueManager;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Queue\Events\JobProcessing;
use Palet\Framework\Queue\Events\JobProcessed;
use Palet\Framework\Queue\Events\JobFailed;
use Palet\Framework\Contracts\Queue\FailedJobRepositoryInterface;

abstract class BaseTestJob implements \Palet\Framework\Contracts\Queue\JobInterface
{
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
    
    public function fire(): void { $this->handle(); }
    public function getName(): string { return static::class; }
    public function attempts(): int { return 1; }
    public function getRawBody(): string { return ''; }
    public function isDeleted(): bool { return false; }
    public function isReleased(): bool { return false; }
    public function delete(): void {}
}

class FailingJob extends BaseTestJob
{
    public function handle(): void
    {
        throw new \Exception('Job failed intentionally');
    }
}

class SuccessfulJob extends BaseTestJob
{
    public function handle(): void
    {
        // success
    }
}

class WorkerTest extends TestCase
{
    public function test_worker_processes_successful_job_and_dispatches_events()
    {
        $manager = new QueueManager();
        $manager->extend('memory', function() {
            return new \Palet\Framework\Queue\Drivers\MemoryQueue();
        });
        
        $manager->connection('memory')->push(new SuccessfulJob(), 'default');
        
        $worker = new Worker($manager);
        
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
                   ->method('dispatch')
                   ->willReturnCallback(function($event) use ($worker) {
                       if ($event instanceof JobProcessed || $event instanceof JobFailed) {
                           $worker->stop();
                       }
                   });
                   
        $worker->setEventDispatcher($dispatcher);
        
        $worker->daemon('memory', 'default');
    }

    public function test_worker_handles_failed_job_and_logs_it()
    {
        $manager = new QueueManager();
        $manager->extend('memory', function() {
            return new \Palet\Framework\Queue\Drivers\MemoryQueue();
        });
        
        $manager->connection('memory')->push(new FailingJob(), 'default');
        
        $worker = new Worker($manager);
        
        $failedRepo = $this->createMock(FailedJobRepositoryInterface::class);
        $failedRepo->expects($this->once())
                   ->method('log')
                   ->with('memory', 'default', $this->anything(), $this->isInstanceOf(\Exception::class));
                   
        $worker->setFailedJobRepository($failedRepo);
        
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
                   ->method('dispatch')
                   ->willReturnCallback(function($event) use ($worker) {
                       if ($event instanceof JobProcessed || $event instanceof JobFailed) {
                           $worker->stop();
                       }
                   });
                   
        $worker->setEventDispatcher($dispatcher);
        
        // Process the failing job (with maxTries = 1 by default, it will fail)
        $worker->daemon('memory', 'default');
    }
}
