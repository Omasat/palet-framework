<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Drivers;

use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Queue\Jobs\MemoryJob;

class MemoryQueue implements QueueInterface
{
    protected array $queue = [];

    public function push(\Palet\Framework\Contracts\Queue\JobInterface $job, string $queue = 'default'): void
    {
        $this->queue[$queue][] = [
            'job_instance' => $job,
            'payload' => '',
            'job' => get_class($job),
            'attempts' => 1,
            'available_at' => time()
        ];
    }
    
    public function pushOn(string $queue, \Palet\Framework\Contracts\Queue\JobInterface $job): void
    {
        $this->push($job, $queue);
    }
    
    public function pushDelayed(\Palet\Framework\Contracts\Queue\JobInterface $job, int $delay, string $queue = 'default'): void
    {
        $this->queue[$queue][] = [
            'job_instance' => $job,
            'payload' => '',
            'job' => get_class($job),
            'attempts' => 1,
            'available_at' => time() + $delay
        ];
    }

    public function later(\DateTimeInterface|\DateInterval|int $delay, string|object $job, mixed $data = '', ?string $queue = null): mixed
    {
        return 0;
    }

    public function pop(?string $queue = null): ?JobInterface
    {
        $queueName = $queue ?? 'default';
        
        if (!isset($this->queue[$queueName]) || empty($this->queue[$queueName])) {
            return null;
        }

        foreach ($this->queue[$queueName] as $key => $jobData) {
            if ($jobData['available_at'] <= time()) {
                unset($this->queue[$queueName][$key]);
                
                // Reindex
                $this->queue[$queueName] = array_values($this->queue[$queueName]);
                
                return $jobData['job_instance'] ?? new MemoryJob($jobData['job'], $jobData['payload'], $jobData['attempts']);
            }
        }

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

    protected function getSeconds(\DateTimeInterface|\DateInterval|int $delay): int
    {
        if ($delay instanceof \DateTimeInterface) {
            return max(0, $delay->getTimestamp() - time());
        }

        if ($delay instanceof \DateInterval) {
            return (new \DateTime())->add($delay)->getTimestamp() - time();
        }

        return $delay;
    }
}
