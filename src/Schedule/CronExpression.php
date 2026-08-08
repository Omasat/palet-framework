<?php

declare(strict_types=1);

namespace Palet\Framework\Schedule;

use Palet\Framework\Contracts\Schedule\CronExpressionInterface;

class CronExpression implements CronExpressionInterface
{
    public function isDue(\DateTimeInterface $time, string $expression): bool
    {
        $segments = explode(' ', $expression);
        
        if (count($segments) !== 5) {
            return false; // For simplicity, we assume standard 5 segment cron format
        }

        $date = [
            (int) $time->format('i'),
            (int) $time->format('H'),
            (int) $time->format('d'),
            (int) $time->format('m'),
            (int) $time->format('w'),
        ];

        foreach ($segments as $index => $segment) {
            if (!$this->matchSegment($segment, $date[$index])) {
                return false;
            }
        }

        return true;
    }

    protected function matchSegment(string $segment, int $value): bool
    {
        if ($segment === '*') {
            return true;
        }
        
        // Handle step values like */5
        if (strpos($segment, '/') !== false) {
            [$range, $step] = explode('/', $segment, 2);
            $step = (int) $step;
            
            if ($range === '*') {
                return $value % $step === 0;
            }
        }

        // Handle exact match
        if (is_numeric($segment)) {
            return (int) $segment === $value;
        }
        
        // Minimalist parsing for tests (ranges like 1-5, lists like 1,2,3 can be added later)
        
        return false;
    }
}
