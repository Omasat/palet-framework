<?php

declare(strict_types=1);

namespace Palet\Framework\Queue;

use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Contracts\Queue\QueueDriverInterface;

class QueueDispatcher
{
    protected array $connections = [];
    protected string $defaultConnection = 'default';

    public function registerConnection(string $name, QueueDriverInterface $driver): void
    {
        $this->connections[$name] = $driver;
    }
    
    public function setDefaultConnection(string $name): void
    {
        $this->defaultConnection = $name;
    }

    public function dispatch(string $queue, JobInterface $job, ?string $connection = null): void
    {
        $driver = $this->connections[$connection ?? $this->defaultConnection] ?? null;
        if ($driver) {
            $driver->push($queue, $job);
        }
    }

    public function dispatchDelayed(string $queue, JobInterface $job, int $delay, ?string $connection = null): void
    {
        $driver = $this->connections[$connection ?? $this->defaultConnection] ?? null;
        if ($driver) {
            $driver->pushDelayed($queue, $job, $delay);
        }
    }
}
