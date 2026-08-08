<?php

declare(strict_types=1);

namespace Palet\Framework\Queue;

use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Queue\Events\JobStarted;
use Palet\Framework\Queue\Events\JobCompleted;
use Palet\Framework\Queue\Events\JobFailed;
use Palet\Framework\Queue\Failure\RetryManager;

class JobProcessor
{
    public function __construct(
        protected RetryManager $retryManager,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function process(JobInterface $job): void
    {
        try {
            if ($this->events) {
                $this->events->dispatch(new JobStarted($job));
            }

            $job->incrementAttempts();
            $job->handle();

            if ($this->events) {
                $this->events->dispatch(new JobCompleted($job));
            }
        } catch (\Throwable $e) {
            if ($this->events) {
                $this->events->dispatch(new JobFailed($job, $e));
            }
            
            $this->retryManager->handleFailure($job, $e);
        }
    }
}
