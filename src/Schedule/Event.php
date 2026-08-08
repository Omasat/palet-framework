<?php

declare(strict_types=1);

namespace Palet\Framework\Schedule;

use Palet\Framework\Contracts\Schedule\EventInterface;
use Palet\Framework\Contracts\Schedule\CronExpressionInterface;
use Palet\Framework\Contracts\Schedule\MutexInterface;

class Event implements EventInterface
{
    protected CronExpressionInterface $cronParser;
    protected MutexInterface $mutex;
    public string $expression = '* * * * *';
    public ?\DateTimeZone $timezone = null;
    protected array $filters = [];
    protected array $rejects = [];
    public bool $withoutOverlapping = false;
    public bool $onOneServer = false;
    
    // Command or Job to execute
    public mixed $command;
    public array $parameters;

    public function __construct(MutexInterface $mutex, CronExpressionInterface $cronParser, mixed $command, array $parameters = [])
    {
        $this->mutex = $mutex;
        $this->cronParser = $cronParser;
        $this->command = $command;
        $this->parameters = $parameters;
    }

    public function cron(string $expression): self
    {
        $this->expression = $expression;
        return $this;
    }

    public function everyMinute(): self
    {
        return $this->cron('* * * * *');
    }

    public function hourly(): self
    {
        return $this->cron('0 * * * *');
    }

    public function daily(): self
    {
        return $this->cron('0 0 * * *');
    }

    public function timezone(\DateTimeZone|string $timezone): self
    {
        $this->timezone = is_string($timezone) ? new \DateTimeZone($timezone) : $timezone;
        return $this;
    }

    public function when(\Closure $callback): self
    {
        $this->filters[] = $callback;
        return $this;
    }

    public function skip(\Closure $callback): self
    {
        $this->rejects[] = $callback;
        return $this;
    }

    public function withoutOverlapping(int $expiresAt = 1440): self
    {
        $this->withoutOverlapping = true;
        // The expiration handling would go into the mutex
        return $this;
    }

    public function onOneServer(): self
    {
        $this->onOneServer = true;
        return $this;
    }

    public function isDue(\DateTimeInterface $time = null): bool
    {
        $time = $time ?: new \DateTimeImmutable('now', $this->timezone);

        if (!$this->cronParser->isDue($time, $this->expression)) {
            return false;
        }

        foreach ($this->filters as $callback) {
            if (!call_user_func($callback)) {
                return false;
            }
        }

        foreach ($this->rejects as $callback) {
            if (call_user_func($callback)) {
                return false;
            }
        }

        return true;
    }
}
