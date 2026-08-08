<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database;

use PDO;

interface ConnectionInterface
{
    /**
     * Get the underlying PDO instance.
     */
    public function getPdo(): PDO;

    /**
     * Get the PDO instance for read operations.
     */
    public function getReadPdo(): PDO;

    /**
     * Get the database connection name.
     */
    public function getName(): string;
    
    /**
     * Start a new database transaction.
     */
    public function beginTransaction(): void;

    /**
     * Commit the active database transaction.
     */
    public function commit(): void;

    /**
     * Rollback the active database transaction.
     */
    public function rollBack(): void;

    /**
     * Get the current transaction level.
     */
    public function transactionLevel(): int;
    
    /**
     * Disconnect from the underlying PDO connection.
     */
    public function disconnect(): void;
    
    /**
     * Reconnect to the database.
     */
    public function reconnect(): void;
}
