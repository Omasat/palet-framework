<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Queue;

interface WorkerInterface
{
    public function process(string $queue): void;
    public function stop(): void;
}
