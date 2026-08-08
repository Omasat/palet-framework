<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Scheduler;

interface SchedulerInterface
{
    public function schedule(TaskInterface $task): ScheduleInterface;
}
