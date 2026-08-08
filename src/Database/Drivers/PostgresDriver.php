<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Drivers;

class PostgresDriver extends AbstractDriver
{
    public function getDsn(array $config): string
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 5432;
        $database = $config['database'] ?? '';
        
        $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
        
        if (isset($config['sslmode'])) {
            $dsn .= ";sslmode={$config['sslmode']}";
        }
        
        return $dsn;
    }
}
