<?php

declare(strict_types=1);

namespace Tests\Database\Orm\UnitOfWork;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\UnitOfWork\TransactionManager;
use PDO;
use Exception;

class MockPDO extends PDO
{
    public array $log = [];
    
    public function __construct() {}
    
    public function beginTransaction(): bool
    {
        $this->log[] = 'BEGIN';
        return true;
    }
    
    public function commit(): bool
    {
        $this->log[] = 'COMMIT';
        return true;
    }
    
    public function rollBack(): bool
    {
        $this->log[] = 'ROLLBACK';
        return true;
    }
    
    public function exec(string $statement): int|false
    {
        $this->log[] = $statement;
        return 1;
    }
}

class TransactionManagerTest extends TestCase
{
    public function test_nested_transactions_use_savepoints()
    {
        $pdo = new MockPDO();
        $tm = new TransactionManager($pdo);
        
        $tm->beginTransaction(); // Level 1 (BEGIN)
        $this->assertEquals(1, $tm->transactionLevel());
        
        $tm->beginTransaction(); // Level 2 (SAVEPOINT trans1)
        $this->assertEquals(2, $tm->transactionLevel());
        
        $tm->commit(); // Level 2 commit (does nothing PDO side)
        $this->assertEquals(1, $tm->transactionLevel());
        
        $tm->commit(); // Level 1 commit (COMMIT)
        $this->assertEquals(0, $tm->transactionLevel());
        
        $this->assertEquals(['BEGIN', 'SAVEPOINT trans1', 'COMMIT'], $pdo->log);
    }

    public function test_transaction_closure_automatically_commits_and_rolls_back()
    {
        $pdo = new MockPDO();
        $tm = new TransactionManager($pdo);
        
        $tm->transaction(function () {
            // successful
        });
        
        $this->assertEquals(['BEGIN', 'COMMIT'], $pdo->log);
        
        $pdo->log = [];
        
        try {
            $tm->transaction(function () {
                throw new Exception('Fail');
            });
        } catch (Exception $e) {}
        
        $this->assertEquals(['BEGIN', 'ROLLBACK'], $pdo->log);
    }
}
