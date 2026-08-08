<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Migrations;

interface MigrationRepositoryInterface
{
    /**
     * Get the completed migrations.
     */
    public function getRan(): array;

    /**
     * Get list of migrations from the latest batch.
     */
    public function getLast(): array;

    /**
     * Log that a migration was run.
     */
    public function log(string $file, int $batch): void;

    /**
     * Remove a migration from the log.
     */
    public function delete(string $file): void;

    /**
     * Get the next migration batch number.
     */
    public function getNextBatchNumber(): int;

    /**
     * Create the migration repository data store.
     */
    public function createRepository(): void;

    /**
     * Determine if the migration repository exists.
     */
    public function repositoryExists(): bool;
    
    /**
     * Lock the repository (if supported).
     */
    public function acquireLock(): bool;
    
    /**
     * Unlock the repository (if supported).
     */
    public function releaseLock(): void;
}
