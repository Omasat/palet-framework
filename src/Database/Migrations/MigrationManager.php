<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations;

class MigrationManager
{
    protected MigrationRunner $runner;

    public function __construct(MigrationRunner $runner)
    {
        $this->runner = $runner;
    }

    public function migrate(): array
    {
        return $this->runner->run();
    }

    public function rollback(): array
    {
        return $this->runner->rollback();
    }

    public function reset(): array
    {
        return $this->runner->reset();
    }
    
    public function fresh(): array
    {
        // Drop all tables logic would go here
        // Then re-run migrations
        return $this->runner->run();
    }
    
    public function refresh(): array
    {
        $this->reset();
        return $this->migrate();
    }
}
