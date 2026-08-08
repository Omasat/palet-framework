<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Drivers;

use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Queue\Jobs\SyncJob;

class SyncQueue implements QueueInterface
{
    public function push(\Palet\Framework\Contracts\Queue\JobInterface $job, string $queue = 'default'): void 
    {
        $job->handle();
    }
    
    public function pushOn(string $queue, \Palet\Framework\Contracts\Queue\JobInterface $job): void 
    {
        $job->handle();
    }
    
    public function pushDelayed(\Palet\Framework\Contracts\Queue\JobInterface $job, int $delay, string $queue = 'default'): void 
    {
        $job->handle();
    }

    public function pop(?string $queue = null): ?JobInterface
    {
        return null;
    }

    protected function createPayload(string|object $job, mixed $data = ''): string
    {
        $payload = [
            'class' => is_object($job) ? get_class($job) : $job,
            'method' => 'handle',
            'data' => is_object($job) ? get_object_vars($job) : $data,
        ];

        return json_encode($payload);
    }
}


