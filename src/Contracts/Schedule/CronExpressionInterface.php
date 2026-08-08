<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Schedule;

interface CronExpressionInterface
{
    /**
     * Determine if the cron expression matches the given time.
     */
    public function isDue(\DateTimeInterface $time, string $expression): bool;
}
