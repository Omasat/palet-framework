<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Workflow;

interface TaskInterface
{
    public function assignTo(string|int $userId): void;
    public function complete(array $data = []): void;
}
