<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Drivers;

use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;
use Redis;
use RuntimeException;

class RedisQueue implements QueueInterface
{
    protected Redis $redis;
    protected string $default;

    public function __construct(Redis $redis, string $default = 'default')
    {
        $this->redis = $redis;
        $this->default = $default;
    }

    public function push(JobInterface $job, string $queue = 'default'): void
    {
        $this->pushOn($queue, $job);
    }

    public function pushOn(string $queue, JobInterface $job): void
    {
        $queue = $this->getQueue($queue);
        $payload = $this->createPayload($job, $queue);

        $this->redis->rpush($queue, $payload);
    }

    public function pushDelayed(JobInterface $job, int $delay, string $queue = 'default'): void
    {
        $queue = $this->getQueue($queue);
        $payload = $this->createPayload($job, $queue);
        $availableAt = time() + $delay;

        $this->redis->zadd($queue . ':delayed', $availableAt, $payload);
    }

    public function pop(?string $queue = null): ?JobInterface
    {
        $queue = $this->getQueue($queue ?: $this->default);

        $this->migrateExpiredJobs($queue . ':delayed', $queue);
        $this->migrateExpiredJobs($queue . ':reserved', $queue);

        $payload = $this->redis->lpop($queue);

        if ($payload !== false) {
            $this->redis->zadd($queue . ':reserved', time() + 90, $payload);
            return $this->unserializeJob($payload);
        }

        return null;
    }

    protected function migrateExpiredJobs(string $from, string $to): void
    {
        $this->redis->watch($from);
        
        $jobs = $this->redis->zrangebyscore($from, '-inf', (string) time());
        
        if (empty($jobs)) {
            $this->redis->unwatch();
            return;
        }

        $pipe = $this->redis->multi();
        $pipe->zremrangebyscore($from, '-inf', (string) time());
        
        foreach ($jobs as $job) {
            $pipe->rpush($to, $job);
        }
        
        $pipe->exec();
    }

    protected function createPayload(JobInterface $job, string $queue): string
    {
        return json_encode([
            'job' => serialize($job),
            'queue' => $queue,
            'attempts' => $job->getAttempts(),
            'id' => $job->getId(),
        ]);
    }

    protected function unserializeJob(string $payload): JobInterface
    {
        $data = json_decode($payload, true);
        
        if (!isset($data['job'])) {
            throw new RuntimeException("Invalid job payload.");
        }
        
        $job = unserialize($data['job']);
        
        if (!$job instanceof JobInterface) {
            throw new RuntimeException("Serialized job does not implement JobInterface.");
        }
        
        return $job;
    }
}
