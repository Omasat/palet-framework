<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Orchestration;

class ExecutionLockManager
{
    protected array $locks = [];

    public function acquire(string $taskId): bool
    {
        if (isset($this->locks[$taskId])) {
            return false;
        }

        $this->locks[$taskId] = true;
        return true;
    }

    public function release(string $taskId): void
    {
        unset($this->locks[$taskId]);
    }
}
