<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Events;

use Palet\Framework\Contracts\Queue\JobInterface;
use Throwable;

class JobFailed
{
    public readonly string $connectionName;
    public readonly JobInterface $job;
    public readonly Throwable $exception;

    public function __construct(string $connectionName, JobInterface $job, Throwable $exception)
    {
        $this->connectionName = $connectionName;
        $this->job = $job;
        $this->exception = $exception;
    }
}
