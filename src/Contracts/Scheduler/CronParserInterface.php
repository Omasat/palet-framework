<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Scheduler;

interface CronParserInterface
{
    public function isDue(string $expression, \DateTimeInterface $time): bool;
    public function getNextRunDate(string $expression, \DateTimeInterface $time): \DateTimeInterface;
}
