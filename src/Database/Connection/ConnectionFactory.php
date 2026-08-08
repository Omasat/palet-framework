<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Connection;

use Palet\Framework\Contracts\Database\DriverInterface;
use Palet\Framework\Database\Drivers\MysqlDriver;
use Palet\Framework\Database\Drivers\PostgresDriver;
use Palet\Framework\Database\Drivers\SqliteDriver;
use PDO;
use InvalidArgumentException;

class ConnectionFactory
{
    public function make(array $config): PDO
    {
        $driver = $this->createDriver($config['driver'] ?? '');
        return $driver->connect($config);
    }

    protected function createDriver(string $driver): DriverInterface
    {
        return match ($driver) {
            'mysql' => new MysqlDriver(),
            'pgsql' => new PostgresDriver(),
            'sqlite' => new SqliteDriver(),
            default => throw new InvalidArgumentException("Unsupported database driver [{$driver}]."),
        };
    }
}
