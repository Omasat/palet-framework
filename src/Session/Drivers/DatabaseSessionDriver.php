<?php

declare(strict_types=1);

namespace Palet\Framework\Session\Drivers;

use SessionHandlerInterface;
use Palet\Framework\Contracts\Database\ConnectionInterface;
use PDO;

class DatabaseSessionDriver implements SessionHandlerInterface
{
    protected ConnectionInterface $connection;
    protected string $table;
    protected int $minutes;

    public function __construct(ConnectionInterface $connection, string $table = 'sessions', int $minutes = 120)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->minutes = $minutes;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $pdo = $this->connection->getPdo();
        $stmt = $pdo->prepare("SELECT payload FROM {$this->table} WHERE id = :id AND last_activity >= :last_activity LIMIT 1");
        
        $lastActivity = time() - ($this->minutes * 60);
        $stmt->execute([
            'id' => $id,
            'last_activity' => $lastActivity
        ]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        if ($result && isset($result->payload)) {
            return base64_decode($result->payload);
        }

        return false;
    }

    public function write(string $id, string $data): bool
    {
        $pdo = $this->connection->getPdo();
        $payload = base64_encode($data);
        $lastActivity = time();

        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        if ($stmt->fetch()) {
            $update = $pdo->prepare("UPDATE {$this->table} SET payload = :payload, last_activity = :last_activity WHERE id = :id");
            return $update->execute([
                'payload' => $payload,
                'last_activity' => $lastActivity,
                'id' => $id
            ]);
        }

        $insert = $pdo->prepare("INSERT INTO {$this->table} (id, payload, last_activity) VALUES (:id, :payload, :last_activity)");
        return $insert->execute([
            'id' => $id,
            'payload' => $payload,
            'last_activity' => $lastActivity
        ]);
    }

    public function destroy(string $id): bool
    {
        $pdo = $this->connection->getPdo();
        $stmt = $pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $pdo = $this->connection->getPdo();
        $stmt = $pdo->prepare("DELETE FROM {$this->table} WHERE last_activity <= :last_activity");
        $stmt->execute([
            'last_activity' => time() - $max_lifetime
        ]);

        return $stmt->rowCount();
    }
}
