<?php

declare(strict_types=1);

namespace Palet\Framework\Queue;

use Palet\Framework\Contracts\Queue\WorkerInterface;
use Palet\Framework\Contracts\Queue\QueueDriverInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Queue\Events\WorkerStarted;
use Palet\Framework\Queue\Events\WorkerStopped;

class WorkerManager implements WorkerInterface
{
    protected bool $shouldStop = false;

    public function __construct(
        protected QueueDriverInterface $driver,
        protected JobProcessor $processor,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function process(string $queue): void
    {
        if ($this->events) {
            $this->events->dispatch(new WorkerStarted($queue));
        }

        while (!$this->shouldStop) {
            $job = $this->driver->pop($queue);

            if ($job) {
                $this->processor->process($job);
                $this->driver->delete($queue, $job);
            } else {
                // Sleep to avoid CPU hogging on empty queues
                usleep(100000); // 100ms
            }
        }
        
        if ($this->events) {
            $this->events->dispatch(new WorkerStopped($queue));
        }
    }

    public function stop(): void
    {
        $this->shouldStop = true;
    }
}
