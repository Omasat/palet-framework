<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler;

use Palet\Framework\Scheduler\Orchestration\TaskOrchestrator;
use DateTimeInterface;

class SchedulerEngine
{
    public function __construct(
        protected SchedulerManager $manager,
        protected TaskOrchestrator $orchestrator
    ) {}

    public function run(DateTimeInterface $time): void
    {
        $schedules = $this->manager->getSchedules();

        foreach ($schedules as $entry) {
            $task = $entry['task'];
            $schedule = $entry['schedule'];

            if ($schedule->isDue($time)) {
                $this->orchestrator->execute($task, $schedule);
            }
        }
    }
}
