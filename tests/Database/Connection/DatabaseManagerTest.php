<?php

declare(strict_types=1);

namespace Tests\Database\Connection;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Connection\DatabaseManager;
use Palet\Framework\Database\Connection\ConnectionPool;
use Palet\Framework\Database\Connection\ConnectionFactory;
use Palet\Framework\Database\Connection\ConnectionHealthMonitor;
use Palet\Framework\Contracts\Database\ConnectionInterface;

class DatabaseManagerTest extends TestCase
{
    public function test_can_resolve_default_connection()
    {
        $config = [
            'default' => 'sqlite',
            'connections' => [
                'sqlite' => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                ]
            ]
        ];

        $factory = new ConnectionFactory();
        $pool = new ConnectionPool($factory);
        $monitor = new ConnectionHealthMonitor();
        $manager = new DatabaseManager($config, $pool, $monitor);

        $connection = $manager->connection();
        
        $this->assertInstanceOf(ConnectionInterface::class, $connection);
        $this->assertEquals('sqlite', $connection->getName());
        $this->assertInstanceOf(\PDO::class, $connection->getPdo());
    }

    public function test_throws_exception_for_unconfigured_connection()
    {
        $config = [
            'default' => 'sqlite',
            'connections' => []
        ];

        $factory = new ConnectionFactory();
        $pool = new ConnectionPool($factory);
        $monitor = new ConnectionHealthMonitor();
        $manager = new DatabaseManager($config, $pool, $monitor);

        $this->expectException(\InvalidArgumentException::class);
        $manager->connection('mysql');
    }
}
