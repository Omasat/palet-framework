<?php

declare(strict_types=1);

namespace Tests\Database\Connection;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Connection\Connection;
use Palet\Framework\Database\Connection\ConnectionPool;
use Palet\Framework\Database\Connection\ConnectionFactory;
use Palet\Framework\Database\Connection\ConnectionHealthMonitor;

class ConnectionTest extends TestCase
{
    public function test_connection_uses_read_pdo_for_read_operations()
    {
        $config = [
            'write' => ['driver' => 'sqlite', 'database' => ':memory:'],
            'read' => ['driver' => 'sqlite', 'database' => ':memory:']
        ];
        
        $factory = new ConnectionFactory();
        $pool = new ConnectionPool($factory);
        $monitor = new ConnectionHealthMonitor();
        
        $connection = new Connection('default', $config, $pool, $monitor);
        
        $writePdo = $connection->getPdo();
        $readPdo = $connection->getReadPdo();
        
        $this->assertNotSame($writePdo, $readPdo);
    }
    
    public function test_read_operations_use_write_pdo_during_transaction()
    {
        $config = [
            'write' => ['driver' => 'sqlite', 'database' => ':memory:'],
            'read' => ['driver' => 'sqlite', 'database' => ':memory:']
        ];
        
        $factory = new ConnectionFactory();
        $pool = new ConnectionPool($factory);
        $monitor = new ConnectionHealthMonitor();
        
        $connection = new Connection('default', $config, $pool, $monitor);
        
        $connection->beginTransaction();
        
        $writePdo = $connection->getPdo();
        $readPdo = $connection->getReadPdo();
        
        $this->assertSame($writePdo, $readPdo);
        
        $connection->rollBack();
    }
}
