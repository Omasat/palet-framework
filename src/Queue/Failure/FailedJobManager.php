<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Failure;

use Palet\Framework\Contracts\Queue\FailedJobInterface;
use Palet\Framework\Contracts\Queue\JobInterface;

class FailedJobManager implements FailedJobInterface
{
    public function log(JobInterface $job, \Throwable $exception): void
    {
        // Log to database or error tracking service
        $job->markAsFailed($exception);
    }
}
