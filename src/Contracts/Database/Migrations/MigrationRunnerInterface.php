<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Migrations;

interface MigrationRunnerInterface
{
    /**
     * Run all pending migrations.
     */
    public function run(): array;

    /**
     * Rollback the last migration batch.
     */
    public function rollback(): array;
    
    /**
     * Rollback all migrations.
     */
    public function reset(): array;
}
