<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Connection;

use Palet\Framework\Contracts\Database\DatabaseManagerInterface;
use Palet\Framework\Contracts\Database\ConnectionInterface;
use InvalidArgumentException;

class DatabaseManager implements DatabaseManagerInterface
{
    protected array $config;
    protected ConnectionPool $pool;
    protected ConnectionHealthMonitor $monitor;
    
    /** @var array<string, ConnectionInterface> */
    protected array $connections = [];
    
    protected string $defaultConnection;

    public function __construct(array $config, ConnectionPool $pool, ConnectionHealthMonitor $monitor)
    {
        $this->config = $config;
        $this->pool = $pool;
        $this->monitor = $monitor;
        $this->defaultConnection = $config['default'] ?? 'default';
    }

    public function connection(?string $name = null): ConnectionInterface
    {
        $name = $name ?: $this->defaultConnection;

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    protected function makeConnection(string $name): ConnectionInterface
    {
        $config = $this->config['connections'][$name] ?? null;

        if (!$config) {
            throw new InvalidArgumentException("Database connection [{$name}] not configured.");
        }

        return new Connection($name, $config, $this->pool, $this->monitor);
    }

    public function reconnect(?string $name = null): ConnectionInterface
    {
        $connection = $this->connection($name);
        $connection->reconnect();
        return $connection;
    }

    public function disconnect(?string $name = null): void
    {
        $name = $name ?: $this->defaultConnection;

        if (isset($this->connections[$name])) {
            $this->connections[$name]->disconnect();
        }
    }

    public function getDefaultConnection(): string
    {
        return $this->defaultConnection;
    }

    public function setDefaultConnection(string $name): void
    {
        $this->defaultConnection = $name;
    }
}
