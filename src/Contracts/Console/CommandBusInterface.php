<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

interface CommandBusInterface
{
    /**
     * Dispatch a command to be executed.
     */
    public function dispatch(string $commandName, InputInterface $input, OutputInterface $output): int;
}
