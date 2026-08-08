<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Events;

use Palet\Framework\Contracts\Console\CommandInterface;

class CommandExecuted
{
    public function __construct(
        public readonly string $commandName,
        public readonly CommandInterface $command,
        public readonly int $exitCode
    ) {}
}
