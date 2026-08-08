<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Events;

use Palet\Framework\Contracts\Scheduler\TaskInterface;
use Palet\Framework\Contracts\Scheduler\ScheduleInterface;

class TaskScheduled
{
    public function __construct(
        public readonly TaskInterface $task,
        public readonly ScheduleInterface $schedule
    ) {}
}
