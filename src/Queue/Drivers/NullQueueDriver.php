<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Drivers;

use Palet\Framework\Contracts\Queue\QueueDriverInterface;
use Palet\Framework\Contracts\Queue\JobInterface;

class NullQueueDriver implements QueueDriverInterface
{
    public function push(string $queue, JobInterface $job): void
    {
        // Do nothing
    }
    
    public function pushDelayed(string $queue, JobInterface $job, int $delay): void
    {
        // Do nothing
    }
    
    public function pop(string $queue): ?JobInterface
    {
        return null;
    }
    
    public function delete(string $queue, JobInterface $job): void
    {
        // Do nothing
    }
}
