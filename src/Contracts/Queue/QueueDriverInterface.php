<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Queue;

interface QueueDriverInterface
{
    public function push(string $queue, JobInterface $job): void;
    
    public function pushDelayed(string $queue, JobInterface $job, int $delay): void;
    
    public function pop(string $queue): ?JobInterface;
    
    public function delete(string $queue, JobInterface $job): void;
}
