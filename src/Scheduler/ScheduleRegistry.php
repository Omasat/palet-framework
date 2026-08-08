<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler;

use Palet\Framework\Contracts\Scheduler\ScheduleInterface;
use Palet\Framework\Contracts\Scheduler\CronParserInterface;
use DateTimeInterface;

class ScheduleRegistry implements ScheduleInterface
{
    protected string $expression = '* * * * *';
    protected bool $withoutOverlapping = false;
    protected int $expiresAt = 1440; // minutes

    public function __construct(protected CronParserInterface $parser) {}

    public function cron(string $expression): self
    {
        $this->expression = $expression;
        return $this;
    }

    public function everyMinute(): self
    {
        return $this->cron('* * * * *');
    }

    public function everyHour(): self
    {
        return $this->cron('0 * * * *');
    }

    public function daily(): self
    {
        return $this->cron('0 0 * * *');
    }

    public function withoutOverlapping(int $expiresAt = 1440): self
    {
        $this->withoutOverlapping = true;
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function isDue(DateTimeInterface $time): bool
    {
        return $this->parser->isDue($this->expression, $time);
    }
    
    public function overlaps(): bool
    {
        return $this->withoutOverlapping;
    }
}
