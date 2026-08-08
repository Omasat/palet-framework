<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Drivers;

class SqliteDriver extends AbstractDriver
{
    public function getDsn(array $config): string
    {
        $database = $config['database'] ?? ':memory:';
        
        if ($database === ':memory:') {
            return 'sqlite::memory:';
        }
        
        return "sqlite:{$database}";
    }
}
