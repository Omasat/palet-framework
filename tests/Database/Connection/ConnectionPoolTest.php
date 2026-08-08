<?php

declare(strict_types=1);

namespace Tests\Database\Connection;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Connection\ConnectionPool;
use Palet\Framework\Database\Connection\ConnectionFactory;

class ConnectionPoolTest extends TestCase
{
    public function test_pool_reuses_connection()
    {
        $factory = new ConnectionFactory();
        $pool = new ConnectionPool($factory);
        
        $config = ['driver' => 'sqlite', 'database' => ':memory:'];
        
        // Checkout first connection
        $pdo1 = $pool->checkout('default', $config);
        
        // Checkin the connection
        $pool->checkin('default', $pdo1);
        
        // Checkout second connection
        $pdo2 = $pool->checkout('default', $config);
        
        // Assert they are the exact same instance
        $this->assertSame($pdo1, $pdo2);
    }
}
