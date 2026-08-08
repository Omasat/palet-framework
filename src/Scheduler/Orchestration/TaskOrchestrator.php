<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Orchestration;

use Palet\Framework\Contracts\Scheduler\TaskInterface;
use Palet\Framework\Scheduler\ScheduleRegistry;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Scheduler\Events\TaskStarted;
use Palet\Framework\Scheduler\Events\TaskCompleted;
use Palet\Framework\Scheduler\Events\TaskFailed;

class TaskOrchestrator
{
    public function __construct(
        protected ExecutionLockManager $lockManager,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function execute(TaskInterface $task, ScheduleRegistry $schedule): void
    {
        $taskId = $task->getId();

        if ($schedule->overlaps()) {
            if (!$this->lockManager->acquire($taskId)) {
                return; // Already running
            }
        }

        try {
            if ($this->events) {
                $this->events->dispatch(new TaskStarted($task));
            }

            $task->run();

            if ($this->events) {
                $this->events->dispatch(new TaskCompleted($task));
            }
        } catch (\Throwable $e) {
            if ($this->events) {
                $this->events->dispatch(new TaskFailed($task, $e));
            }
        } finally {
            if ($schedule->overlaps()) {
                $this->lockManager->release($taskId);
            }
        }
    }
}
