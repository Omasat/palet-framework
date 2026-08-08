<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Events;

use Palet\Framework\Contracts\Console\CommandInterface;
use Throwable;

class CommandFailed
{
    public function __construct(
        public readonly string $commandName,
        public readonly CommandInterface $command,
        public readonly int $exitCode,
        public readonly ?Throwable $exception = null
    ) {}
}
