<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Events;

class JobQueued
{
    public readonly string $connectionName;
    public readonly mixed $job;
    public readonly mixed $data;

    public function __construct(string $connectionName, mixed $job, mixed $data)
    {
        $this->connectionName = $connectionName;
        $this->job = $job;
        $this->data = $data;
    }
}
