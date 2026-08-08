<?php

declare(strict_types=1);

namespace Tests\Queue\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Queue\Drivers\DatabaseQueue;
use Palet\Framework\Contracts\Database\ConnectionInterface;
use Palet\Framework\Contracts\Queue\JobInterface;
use PDO;
use PDOStatement;
use stdClass;

class DatabaseQueueTest extends TestCase
{
    public function test_push_and_pop()
    {
        $connection = $this->createMock(ConnectionInterface::class);
        $pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
        $job = $this->createMock(JobInterface::class);
        
        $connection->method('getPdo')->willReturn($pdo);
        $pdo->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        
        $record = new stdClass();
        $record->id = 1;
        $record->payload = serialize($job);
        
        $stmt->method('fetch')->willReturn($record);
        
        $queue = new DatabaseQueue($connection);
        
        $queue->push($job);
        
        $popped = $queue->pop();
        $this->assertInstanceOf(JobInterface::class, $popped);
    }
}
