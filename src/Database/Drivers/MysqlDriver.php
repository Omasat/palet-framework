<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Drivers;

class MysqlDriver extends AbstractDriver
{
    public function getDsn(array $config): string
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['database'] ?? '';
        
        $dsn = "mysql:host={$host};port={$port};dbname={$database}";
        
        if (isset($config['charset'])) {
            $dsn .= ";charset={$config['charset']}";
        }
        
        return $dsn;
    }
}
