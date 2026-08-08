<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Queue;

interface FailedJobInterface
{
    public function log(JobInterface $job, \Throwable $exception): void;
}
