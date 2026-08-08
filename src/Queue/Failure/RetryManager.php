<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Failure;

use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Queue\Events\JobRetried;

class RetryManager
{
    public function __construct(
        protected QueueInterface $queue,
        protected FailedJobManager $failedJobManager,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function handleFailure(JobInterface $job, \Throwable $exception): void
    {
        if ($job->getAttempts() < $job->getMaxTries()) {
            // Retry
            $delay = $this->calculateDelay($job);
            
            if ($this->events) {
                $this->events->dispatch(new JobRetried($job));
            }
            
            $this->queue->pushDelayed($job, $delay, $job->getQueue());
        } else {
            // Fails permanently
            $this->failedJobManager->log($job, $exception);
        }
    }

    protected function calculateDelay(JobInterface $job): int
    {
        // Exponential backoff or custom delay logic can be implemented here
        return $job->getDelay() > 0 ? $job->getDelay() : 5;
    }
}
