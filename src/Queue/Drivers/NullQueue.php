<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Drivers;

use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;

class NullQueue implements QueueInterface
{
    public function push(string|object $job, mixed $data = '', ?string $queue = null): mixed
    {
        return 0;
    }

    public function later(\DateTimeInterface|\DateInterval|int $delay, string|object $job, mixed $data = '', ?string $queue = null): mixed
    {
        return 0;
    }

    public function pop(?string $queue = null): ?JobInterface
    {
        return null;
    }
}
