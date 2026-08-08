<?php

declare(strict_types=1);

namespace Palet\Framework\Queue;

use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Queue\Events\JobQueued;

class QueueManager implements QueueInterface
{
    protected array $connections = [];
    protected array $resolvers = [];

    public function __construct(
        protected ?QueueDispatcher $dispatcher = null,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function extend(string $driver, \Closure $resolver): void
    {
        $this->resolvers[$driver] = $resolver;
    }

    public function connection(string $name = 'default'): QueueInterface
    {
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->resolvers[$name]();
        }

        return $this->connections[$name];
    }

    public function push(JobInterface $job, string $queue = 'default'): void
    {
        $this->pushOn($queue, $job);
    }

    public function pushOn(string $queue, JobInterface $job): void
    {
        $job->setQueue($queue);
        
        $this->dispatcher->dispatch($queue, $job);
        
        if ($this->events) {
            $this->events->dispatch(new JobQueued($job, $queue));
        }
    }

    public function pushDelayed(JobInterface $job, int $delay, string $queue = 'default'): void
    {
        $job->setDelay($delay);
        $job->setQueue($queue);
        
        $this->dispatcher->dispatchDelayed($queue, $job, $delay);
        
        if ($this->events) {
            $this->events->dispatch(new JobQueued($job, $queue));
        }
    }
}
