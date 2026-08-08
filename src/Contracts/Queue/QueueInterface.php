<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Queue;

interface QueueInterface
{
    public function push(JobInterface $job, string $queue = 'default'): void;
    public function pushOn(string $queue, JobInterface $job): void;
    public function pushDelayed(JobInterface $job, int $delay, string $queue = 'default'): void;
}
