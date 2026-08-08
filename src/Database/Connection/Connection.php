<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Connection;

use Palet\Framework\Contracts\Database\ConnectionInterface;
use PDO;
use RuntimeException;
use Throwable;

class Connection implements ConnectionInterface
{
    protected ?PDO $pdo = null;
    protected ?PDO $readPdo = null;
    protected string $name;
    protected array $config;
    protected ConnectionPool $pool;
    protected ConnectionHealthMonitor $monitor;
    
    protected int $transactions = 0;

    public function __construct(string $name, array $config, ConnectionPool $pool, ConnectionHealthMonitor $monitor)
    {
        $this->name = $name;
        $this->config = $config;
        $this->pool = $pool;
        $this->monitor = $monitor;
    }

    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->resolvePdo($this->config['write'] ?? $this->config);
        }

        return $this->pdo;
    }

    public function getReadPdo(): PDO
    {
        if ($this->transactions > 0) {
            return $this->getPdo(); // During a transaction, force reads to the write connection
        }
        
        if ($this->readPdo === null) {
            $readConfig = $this->config['read'] ?? $this->config['write'] ?? $this->config;
            $this->readPdo = $this->resolvePdo($readConfig);
        }

        return $this->readPdo;
    }
    
    protected function resolvePdo(array $config): PDO
    {
        $pdo = $this->pool->checkout($this->name, $config);
        
        if (!$this->monitor->isAlive($pdo)) {
            $pdo = $this->pool->checkout($this->name, $config); // Try reconnect
        }
        
        return $pdo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function beginTransaction(): void
    {
        if ($this->transactions === 0) {
            $this->getPdo()->beginTransaction();
        } elseif ($this->transactions >= 1) {
            // Nested transaction savepoint logic can go here in the future
        }
        
        $this->transactions++;
    }

    public function commit(): void
    {
        if ($this->transactions === 1) {
            $this->getPdo()->commit();
        }
        
        $this->transactions = max(0, $this->transactions - 1);
    }

    public function rollBack(): void
    {
        if ($this->transactions === 1) {
            $this->getPdo()->rollBack();
        } else {
            // Rollback to savepoint logic
        }
        
        $this->transactions = max(0, $this->transactions - 1);
    }

    public function transactionLevel(): int
    {
        return $this->transactions;
    }

    public function disconnect(): void
    {
        if ($this->pdo) {
            $this->pool->checkin($this->name, $this->pdo);
            $this->pdo = null;
        }
        
        if ($this->readPdo && $this->readPdo !== $this->pdo) {
            $this->pool->checkin($this->name . '_read', $this->readPdo);
            $this->readPdo = null;
        }
    }

    public function reconnect(): void
    {
        $this->disconnect();
        $this->getPdo();
    }
}
