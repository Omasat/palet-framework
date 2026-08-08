<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\UnitOfWork;

use Closure;

interface TransactionManagerInterface
{
    /**
     * Begin a new database transaction.
     */
    public function beginTransaction(): void;

    /**
     * Commit the active database transaction.
     */
    public function commit(): void;

    /**
     * Rollback the active database transaction.
     */
    public function rollback(): void;

    /**
     * Execute a closure within a transaction.
     */
    public function transaction(Closure $callback): mixed;

    /**
     * Get the current transaction level.
     */
    public function transactionLevel(): int;
}
