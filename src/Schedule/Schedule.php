<?php

declare(strict_types=1);

namespace Palet\Framework\Schedule;

use Palet\Framework\Contracts\Schedule\ScheduleInterface;
use Palet\Framework\Contracts\Schedule\EventInterface;
use Palet\Framework\Contracts\Schedule\MutexInterface;
use Palet\Framework\Contracts\Schedule\CronExpressionInterface;

class Schedule implements ScheduleInterface
{
    protected MutexInterface $mutex;
    protected CronExpressionInterface $cronParser;
    protected array $events = [];

    public function __construct(MutexInterface $mutex, CronExpressionInterface $cronParser)
    {
        $this->mutex = $mutex;
        $this->cronParser = $cronParser;
    }

    public function call(callable|string $callback, array $parameters = []): EventInterface
    {
        $event = new Event($this->mutex, $this->cronParser, $callback, $parameters);
        $this->events[] = $event;
        return $event;
    }

    public function command(string $command, array $parameters = []): EventInterface
    {
        $event = new Event($this->mutex, $this->cronParser, $command, $parameters);
        $this->events[] = $event;
        return $event;
    }

    public function job(string|object $job, ?string $queue = null): EventInterface
    {
        $event = new Event($this->mutex, $this->cronParser, $job, ['queue' => $queue]);
        $this->events[] = $event;
        return $event;
    }

    public function events(): array
    {
        return $this->events;
    }

    public function dueEvents(\DateTimeInterface $time = null): array
    {
        return array_filter($this->events, function (EventInterface $event) use ($time) {
            return $event->isDue($time);
        });
    }
}
