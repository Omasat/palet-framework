<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Schedule;

interface EventInterface
{
    /**
     * The Cron expression representing the event's frequency.
     */
    public function cron(string $expression): self;

    /**
     * Schedule the event to run every minute.
     */
    public function everyMinute(): self;

    /**
     * Schedule the event to run hourly.
     */
    public function hourly(): self;

    /**
     * Schedule the event to run daily.
     */
    public function daily(): self;

    /**
     * Set the timezone the date should be evaluated on.
     */
    public function timezone(\DateTimeZone|string $timezone): self;

    /**
     * Register a callback to further filter the schedule.
     */
    public function when(\Closure $callback): self;

    /**
     * Register a callback to further filter the schedule.
     */
    public function skip(\Closure $callback): self;

    /**
     * Do not allow the event to overlap across multiple instances.
     */
    public function withoutOverlapping(int $expiresAt = 1440): self;

    /**
     * Run the event on one server only.
     */
    public function onOneServer(): self;

    /**
     * Determine if the given event should run based on the Cron expression.
     */
    public function isDue(\DateTimeInterface $time = null): bool;
}
