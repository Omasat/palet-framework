<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Scheduler;

interface ScheduleInterface
{
    public function cron(string $expression): self;
    public function everyMinute(): self;
    public function everyHour(): self;
    public function daily(): self;
    
    public function withoutOverlapping(int $expiresAt = 1440): self;
    
    public function isDue(\DateTimeInterface $time): bool;
}
