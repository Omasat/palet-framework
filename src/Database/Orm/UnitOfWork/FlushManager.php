<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\UnitOfWork;

use Palet\Framework\Contracts\Database\Orm\UnitOfWork\FlushManagerInterface;
use Palet\Framework\Contracts\Database\Orm\UnitOfWork\TransactionManagerInterface;

class FlushManager implements FlushManagerInterface
{
    protected TransactionManagerInterface $transactionManager;

    public function __construct(TransactionManagerInterface $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function flush(array $newObjects, array $dirtyObjects, array $removedObjects): void
    {
        $this->transactionManager->transaction(function () use ($newObjects, $dirtyObjects, $removedObjects) {
            $this->executeInserts($newObjects);
            $this->executeUpdates($dirtyObjects);
            $this->executeDeletes($removedObjects);
        });
    }

    protected function executeInserts(array $objects): void
    {
        // Mock SQL Generation: INSERT INTO table (keys) VALUES (values)
    }

    protected function executeUpdates(array $objects): void
    {
        // Mock SQL Generation: UPDATE table SET key=value WHERE id=id
    }

    protected function executeDeletes(array $objects): void
    {
        // Mock SQL Generation: DELETE FROM table WHERE id=id
    }
}
