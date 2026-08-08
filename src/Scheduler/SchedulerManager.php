<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler;

use Palet\Framework\Contracts\Scheduler\SchedulerInterface;
use Palet\Framework\Contracts\Scheduler\ScheduleInterface;
use Palet\Framework\Contracts\Scheduler\TaskInterface;
use Palet\Framework\Contracts\Scheduler\CronParserInterface;

class SchedulerManager implements SchedulerInterface
{
    /** @var array<array{task: TaskInterface, schedule: ScheduleRegistry}> */
    protected array $schedules = [];

    public function __construct(protected CronParserInterface $parser) {}

    public function schedule(TaskInterface $task): ScheduleInterface
    {
        $schedule = new ScheduleRegistry($this->parser);
        $this->schedules[] = [
            'task' => $task,
            'schedule' => $schedule
        ];
        
        return $schedule;
    }
    
    public function getSchedules(): array
    {
        return $this->schedules;
    }
}
