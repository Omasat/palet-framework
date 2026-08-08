<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Jobs;

use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Queue\State\JobStatus;

abstract class QueueableJob implements JobInterface
{
    protected string $id;
    protected string $queue = 'default';
    protected int $delay = 0;
    protected int $maxTries = 3;
    protected int $attempts = 0;
    protected JobStatus $status = JobStatus::PENDING;
    protected ?\Throwable $failedException = null;

    public function __construct()
    {
        $this->id = uniqid('job_');
    }

    abstract public function handle(): void;

    public function getId(): string
    {
        return $this->id;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function setQueue(string $queue): void
    {
        $this->queue = $queue;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function setDelay(int $delay): void
    {
        $this->delay = $delay;
    }

    public function getMaxTries(): int
    {
        return $this->maxTries;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function incrementAttempts(): void
    {
        $this->attempts++;
    }

    public function release(int $delay = 0): void
    {
        // Pushes the job back onto the queue
        $this->setDelay($delay);
    }

    public function markAsFailed(\Throwable $exception): void
    {
        $this->status = JobStatus::FAILED;
        $this->failedException = $exception;
    }
}
