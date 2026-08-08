<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Events;

use Palet\Framework\Contracts\Scheduler\TaskInterface;

class TaskFailed
{
    public function __construct(
        public readonly TaskInterface $task,
        public readonly \Throwable $exception
    ) {}
}
