<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Drivers;

use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;
use Palet\Framework\Contracts\Database\ConnectionInterface;
use PDO;
use RuntimeException;

class DatabaseQueue implements QueueInterface
{
    protected ConnectionInterface $connection;
    protected string $table;
    protected string $default;

    public function __construct(ConnectionInterface $connection, string $table = 'jobs', string $default = 'default')
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->default = $default;
    }

    public function push(JobInterface $job, string $queue = 'default'): void
    {
        $this->pushOn($queue, $job);
    }

    public function pushOn(string $queue, JobInterface $job): void
    {
        $this->pushDelayed($job, 0, $queue);
    }

    public function pushDelayed(JobInterface $job, int $delay, string $queue = 'default'): void
    {
        $queue = $queue ?: $this->default;
        $payload = serialize($job);
        $availableAt = time() + $delay;
        $createdAt = time();

        $pdo = $this->connection->getPdo();
        $stmt = $pdo->prepare("INSERT INTO {$this->table} (queue, payload, attempts, reserved_at, available_at, created_at) VALUES (:queue, :payload, 0, NULL, :available_at, :created_at)");
        
        $stmt->execute([
            'queue' => $queue,
            'payload' => $payload,
            'available_at' => $availableAt,
            'created_at' => $createdAt,
        ]);
    }

    public function pop(?string $queue = null): ?JobInterface
    {
        $queue = $queue ?: $this->default;
        $pdo = $this->connection->getPdo();
        
        // This is a simplified pop mechanism. In production, row-level locking (SELECT ... FOR UPDATE) is needed.
        $this->connection->beginTransaction();
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE queue = :queue AND reserved_at IS NULL AND available_at <= :available_at ORDER BY id ASC LIMIT 1 FOR UPDATE");
            $stmt->execute([
                'queue' => $queue,
                'available_at' => time()
            ]);
            
            $jobRecord = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($jobRecord) {
                // Reserve it
                $update = $pdo->prepare("UPDATE {$this->table} SET reserved_at = :reserved_at, attempts = attempts + 1 WHERE id = :id");
                $update->execute([
                    'reserved_at' => time(),
                    'id' => $jobRecord->id
                ]);
                
                $this->connection->commit();
                
                $job = unserialize($jobRecord->payload);
                if (!$job instanceof JobInterface) {
                    throw new RuntimeException("Serialized job does not implement JobInterface.");
                }
                
                return $job;
            }
            
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return null;
    }
}
