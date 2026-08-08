<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Events;

use Palet\Framework\Contracts\Scheduler\TaskInterface;

class TaskStarted
{
    public function __construct(public readonly TaskInterface $task) {}
}
