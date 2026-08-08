<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Events;

use Palet\Framework\Contracts\Console\CommandInterface;

class CommandStarting
{
    public readonly string $commandName;
    public readonly CommandInterface $command;

    public function __construct(string $commandName, CommandInterface $command)
    {
        $this->commandName = $commandName;
        $this->command = $command;
    }
}
