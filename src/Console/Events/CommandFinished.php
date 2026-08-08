<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Events;

use Palet\Framework\Contracts\Console\CommandInterface;

class CommandFinished
{
    public readonly string $commandName;
    public readonly CommandInterface $command;
    public readonly int $exitCode;

    public function __construct(string $commandName, CommandInterface $command, int $exitCode)
    {
        $this->commandName = $commandName;
        $this->command = $command;
        $this->exitCode = $exitCode;
    }
}
