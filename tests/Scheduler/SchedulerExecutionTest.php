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

class SchedulerExecutionTest extends TestCase
{
    public function test_scheduler_runs_due_tasks()
    {
        $parser = new CronExpressionParser();
        $manager = new SchedulerManager($parser);
        $lockManager = new ExecutionLockManager();
        $orchestrator = new TaskOrchestrator($lockManager);
        $engine = new SchedulerEngine($manager, $orchestrator);

        $executed = false;
        $task = new CallbackTask(function() use (&$executed) {
            $executed = true;
        });

        // Every minute
        $manager->schedule($task)->everyMinute();
        
        $time = new DateTimeImmutable('2026-01-01 12:00:00');
        $engine->run($time);
        
        $this->assertTrue($executed);
    }
}
