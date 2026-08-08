<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Events;

use Palet\Framework\Workflow\WorkflowInstance;

class TaskAssigned
{
    public function __construct(
        public readonly WorkflowInstance $instance,
        public readonly string $taskId,
        public readonly string|int $userId
    ) {}
}
