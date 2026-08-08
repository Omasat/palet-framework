<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\UnitOfWork;

use Palet\Framework\Contracts\Database\Orm\UnitOfWork\TransactionManagerInterface;
use Closure;
use Exception;
use Throwable;
use PDO;

class TransactionManager implements TransactionManagerInterface
{
    protected PDO $pdo;
    protected int $transactions = 0;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function beginTransaction(): void
    {
        if ($this->transactions == 0) {
            $this->pdo->beginTransaction();
        } else {
            $this->pdo->exec("SAVEPOINT trans{$this->transactions}");
        }
        $this->transactions++;
    }

    public function commit(): void
    {
        if ($this->transactions == 1) {
            $this->pdo->commit();
        }
        $this->transactions = max(0, $this->transactions - 1);
    }

    public function rollback(): void
    {
        if ($this->transactions == 1) {
            $this->pdo->rollBack();
        } elseif ($this->transactions > 1) {
            $level = $this->transactions - 1;
            $this->pdo->exec("ROLLBACK TO SAVEPOINT trans{$level}");
        }
        $this->transactions = max(0, $this->transactions - 1);
    }

    public function transaction(Closure $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception | Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function transactionLevel(): int
    {
        return $this->transactions;
    }
}
