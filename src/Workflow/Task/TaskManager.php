<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Task;

use Palet\Framework\Workflow\WorkflowInstance;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Workflow\Events\TaskAssigned;

class TaskManager
{
    public function __construct(protected ?EventDispatcherInterface $events = null) {}

    public function assignTask(WorkflowInstance $instance, string $taskId, string|int $userId): void
    {
        if ($this->events) {
            $this->events->dispatch(new TaskAssigned($instance, $taskId, $userId));
        }
    }
}
