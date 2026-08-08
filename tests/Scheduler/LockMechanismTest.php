<?php

declare(strict_types=1);

namespace Tests\Scheduler;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Scheduler\SchedulerManager;
use Palet\Framework\Scheduler\SchedulerEngine;
use Palet\Framework\Scheduler\Orchestration\TaskOrchestrator;
use Palet\Framework\Scheduler\Orchestration\ExecutionLockManager;
use Palet\Framework\Scheduler\CronExpressionParser;
use Palet\Framework\Scheduler\Tasks\CallbackTask;
use DateTimeImmutable;

class LockMechanismTest extends TestCase
{
    public function test_prevent_overlapping_tasks()
    {
        $parser = new CronExpressionParser();
        $manager = new SchedulerManager($parser);
        $lockManager = new ExecutionLockManager();
        $orchestrator = new TaskOrchestrator($lockManager);
        $engine = new SchedulerEngine($manager, $orchestrator);

        $executions = 0;
        
        // Simulating a long-running task by not releasing the lock intentionally in our mock
        $task = new CallbackTask(function() use (&$executions, $lockManager, &$task) {
            $executions++;
            // Simulate task is still running by keeping the lock,
            // normally orchestrator releases it, but let's test acquire logic.
        });
        
        $schedule = clone $manager->schedule($task)->everyMinute()->withoutOverlapping();
        
        // Force acquire lock manually to simulate it's running elsewhere
        $lockManager->acquire($task->getId());
        
        $time = new DateTimeImmutable('2026-01-01 12:00:00');
        $engine->run($time); // Should skip because lock is held
        
        $this->assertEquals(0, $executions);
    }
}
