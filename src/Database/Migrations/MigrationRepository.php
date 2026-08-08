<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations;

use Palet\Framework\Contracts\Database\Migrations\MigrationRepositoryInterface;

class MigrationRepository implements MigrationRepositoryInterface
{
    /**
     * A mock array simulating a database table for testing without an active connection.
     * Schema: ['id', 'migration', 'batch']
     */
    protected array $table = [];
    protected bool $isLocked = false;
    protected bool $tableExists = false;

    public function getRan(): array
    {
        return array_column($this->table, 'migration');
    }

    public function getLast(): array
    {
        if (empty($this->table)) {
            return [];
        }
        
        $maxBatch = max(array_column($this->table, 'batch'));
        
        return array_filter($this->table, fn($row) => $row['batch'] === $maxBatch);
    }

    public function log(string $file, int $batch): void
    {
        $this->table[] = [
            'id' => count($this->table) + 1,
            'migration' => $file,
            'batch' => $batch,
        ];
    }

    public function delete(string $file): void
    {
        $this->table = array_filter($this->table, fn($row) => $row['migration'] !== $file);
        $this->table = array_values($this->table); // re-index
    }

    public function getNextBatchNumber(): int
    {
        if (empty($this->table)) {
            return 1;
        }
        
        return max(array_column($this->table, 'batch')) + 1;
    }

    public function createRepository(): void
    {
        $this->tableExists = true;
    }

    public function repositoryExists(): bool
    {
        return $this->tableExists;
    }
    
    public function acquireLock(): bool
    {
        if ($this->isLocked) {
            return false;
        }
        $this->isLocked = true;
        return true;
    }
    
    public function releaseLock(): void
    {
        $this->isLocked = false;
    }
}
