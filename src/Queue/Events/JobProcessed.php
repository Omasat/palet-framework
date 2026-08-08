<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Events;

use Palet\Framework\Contracts\Queue\JobInterface;

class JobProcessed
{
    public readonly string $connectionName;
    public readonly JobInterface $job;

    public function __construct(string $connectionName, JobInterface $job)
    {
        $this->connectionName = $connectionName;
        $this->job = $job;
    }
}
