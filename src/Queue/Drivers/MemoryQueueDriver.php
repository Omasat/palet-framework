<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Drivers;

use Palet\Framework\Contracts\Queue\QueueDriverInterface;
use Palet\Framework\Contracts\Queue\JobInterface;

class MemoryQueueDriver implements QueueDriverInterface
{
    protected array $queues = [];
    protected array $delayed = [];

    public function push(string $queue, JobInterface $job): void
    {
        $this->queues[$queue][] = $job;
    }
    
    public function pushDelayed(string $queue, JobInterface $job, int $delay): void
    {
        $this->delayed[$queue][] = [
            'job' => $job,
            'available_at' => time() + $delay
        ];
    }
    
    public function pop(string $queue): ?JobInterface
    {
        // Process delayed jobs first
        if (isset($this->delayed[$queue])) {
            foreach ($this->delayed[$queue] as $key => $delayedJob) {
                if (time() >= $delayedJob['available_at']) {
                    unset($this->delayed[$queue][$key]);
                    return $delayedJob['job'];
                }
            }
        }

        if (!empty($this->queues[$queue])) {
            return array_shift($this->queues[$queue]);
        }
        
        return null;
    }
    
    public function delete(string $queue, JobInterface $job): void
    {
        // For memory driver, pop already removes it.
    }
}
