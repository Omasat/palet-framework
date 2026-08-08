<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Jobs;

class SyncJob extends Job
{
    protected string $job;
    protected string $payload;

    public function __construct(string|object $job, string $payload)
    {
        $this->job = is_object($job) ? get_class($job) : $job;
        $this->payload = $payload;
    }

    public function fire(): void
    {
        $payload = json_decode($this->payload, true);
        
        $this->resolveAndFire($payload);
    }

    public function attempts(): int
    {
        return 1;
    }

    public function getRawBody(): string
    {
        return $this->payload;
    }

    public function getName(): string
    {
        return $this->job;
    }
}
