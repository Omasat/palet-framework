<?php

declare(strict_types=1);

namespace Palet\Framework\Queue;

use Palet\Framework\Contracts\Queue\WorkerInterface;
use Palet\Framework\Contracts\Queue\FailedJobRepositoryInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Queue\Events\JobProcessing;
use Palet\Framework\Queue\Events\JobProcessed;
use Palet\Framework\Queue\Events\JobFailed;
use Throwable;

class Worker implements WorkerInterface
{
    protected QueueManager $manager;
    protected ?EventDispatcherInterface $events = null;
    protected ?FailedJobRepositoryInterface $failedJobs = null;

    protected bool $shouldQuit = false;

    public function __construct(QueueManager $manager)
    {
        $this->manager = $manager;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function setFailedJobRepository(FailedJobRepositoryInterface $repository): void
    {
        $this->failedJobs = $repository;
    }

    public function daemon(string $connectionName, string $queue, array $options = []): void
    {
        $this->listenForSignals();

        while (true) {
            if ($this->shouldQuit) {
                break;
            }

            $job = $this->manager->connection($connectionName)->pop($queue);

            if ($job) {
                $this->processJob($connectionName, $job, $options);
            } else {
                $this->sleep($options['sleep'] ?? 3);
            }
        }
    }

    protected function sleep(int $seconds): void
    {
        sleep($seconds);
    }

    protected function listenForSignals(): void
    {
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            
            pcntl_signal(SIGTERM, function () {
                $this->shouldQuit = true;
            });
            
            pcntl_signal(SIGINT, function () {
                $this->shouldQuit = true;
            });
            
            pcntl_signal(SIGQUIT, function () {
                $this->shouldQuit = true;
            });
        }
    }

    public function runNextJob(string $connectionName, string $queue, array $options = []): void
    {
        $job = $this->manager->connection($connectionName)->pop($queue);

        if ($job) {
            $this->processJob($connectionName, $job, $options);
        }
    }

    public function process(string $queue): void
    {
        $this->runNextJob('default', $queue);
    }
    
    public function stop(): void
    {
        $this->shouldQuit = true;
    }

    protected function processJob(string $connectionName, $job, array $options): void
    {
        try {
            if ($this->events) {
                $this->events->dispatch(new JobProcessing($connectionName, $job));
            }

            $job->fire();

            if (!$job->isDeleted() && !$job->isReleased()) {
                $job->delete();
            }

            if ($this->events) {
                $this->events->dispatch(new JobProcessed($connectionName, $job));
            }
        } catch (Throwable $e) {
            $this->handleJobException($connectionName, $job, $options, $e);
        }
    }

    protected function handleJobException(string $connectionName, $job, array $options, Throwable $e): void
    {
        if (!$job->isDeleted() && !$job->isReleased()) {
            // Default max tries logic
            $maxTries = $options['maxTries'] ?? 1;

            if ($job->attempts() >= $maxTries) {
                $job->delete();
                $this->failJob($connectionName, $job, $e);
            } else {
                $job->release();
            }
        }
    }

    protected function failJob(string $connectionName, $job, Throwable $e): void
    {
        if ($this->failedJobs) {
            $this->failedJobs->log($connectionName, 'default', $job->getRawBody(), $e);
        }

        if ($this->events) {
            $this->events->dispatch(new JobFailed($connectionName, $job, $e));
        }
    }
}
