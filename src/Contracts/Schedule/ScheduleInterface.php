<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Schedule;

interface ScheduleInterface
{
    /**
     * Add a new callback event to the schedule.
     */
    public function call(callable|string $callback, array $parameters = []): EventInterface;

    /**
     * Add a new command event to the schedule.
     */
    public function command(string $command, array $parameters = []): EventInterface;

    /**
     * Add a new job event to the schedule.
     */
    public function job(string|object $job, ?string $queue = null): EventInterface;

    /**
     * Get all of the events on the schedule.
     */
    public function events(): array;

    /**
     * Get all of the events on the schedule that are due.
     */
    public function dueEvents(\DateTimeInterface $time = null): array;
}
