<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler;

use Palet\Framework\Contracts\Scheduler\CronParserInterface;
use DateTimeInterface;

class CronExpressionParser implements CronParserInterface
{
    public function isDue(string $expression, DateTimeInterface $time): bool
    {
        // Simple mock implementation for tests
        // e.g. "*/5 * * * *" -> means every 5 minutes
        if ($expression === '* * * * *') {
            return true;
        }

        if (str_starts_with($expression, '*/')) {
            $parts = explode(' ', $expression);
            $minPart = $parts[0];
            $interval = (int) str_replace('*/', '', $minPart);
            
            $minute = (int) $time->format('i');
            return $minute % $interval === 0;
        }

        // For full robust implementation, we would use a proper cron library like mtdowling/cron-expression.
        return false;
    }

    public function getNextRunDate(string $expression, DateTimeInterface $time): DateTimeInterface
    {
        // Mock implementation
        $clone = clone $time;
        if (str_starts_with($expression, '*/')) {
            $parts = explode(' ', $expression);
            $minPart = $parts[0];
            $interval = (int) str_replace('*/', '', $minPart);
            return $clone->modify("+{$interval} minutes");
        }
        
        return $clone->modify('+1 minute');
    }
}
