<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Jobs;

class MemoryJob extends Job
{
    protected string $job;
    protected string $payload;
    protected int $attempts;

    public function __construct(string $job, string $payload, int $attempts = 1)
    {
        $this->job = $job;
        $this->payload = $payload;
        $this->attempts = $attempts;
    }

    public function fire(): void
    {
        $payload = json_decode($this->payload, true);
        
        $this->resolveAndFire($payload);
    }

    public function attempts(): int
    {
        return $this->attempts;
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
