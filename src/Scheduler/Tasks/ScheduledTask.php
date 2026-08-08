<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Tasks;

use Palet\Framework\Contracts\Scheduler\TaskInterface;

abstract class ScheduledTask implements TaskInterface
{
    protected string $id;

    public function __construct()
    {
        $this->id = uniqid('task_');
    }

    public function getId(): string
    {
        return $this->id;
    }
}
