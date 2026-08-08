<?php

declare(strict_types=1);

namespace Tests\Session\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Session\Drivers\DatabaseSessionDriver;
use Palet\Framework\Contracts\Database\ConnectionInterface;
use PDO;
use PDOStatement;

class DatabaseSessionDriverTest extends TestCase
{
    public function test_read_and_write()
    {
        $connection = $this->createMock(ConnectionInterface::class);
        $pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
        
        $connection->method('getPdo')->willReturn($pdo);
        
        $pdo->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false); // Simulate not exists for insert
        
        $driver = new DatabaseSessionDriver($connection);
        
        $this->assertTrue($driver->write('session_1', 'data'));
    }
}
