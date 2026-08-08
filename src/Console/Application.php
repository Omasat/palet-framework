<?php

declare(strict_types=1);

namespace Palet\Framework\Console;

use Palet\Framework\Contracts\Console\CommandInterface;
use Palet\Framework\Contracts\Console\InputInterface;
use Palet\Framework\Contracts\Console\OutputInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Console\Exceptions\CommandNotFoundException;
use Palet\Framework\Console\Events\CommandStarting;
use Palet\Framework\Console\Events\CommandFinished;

class Application
{
    protected array $commands = [];
    protected ?EventDispatcherInterface $events = null;

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function add(CommandInterface $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $commandName = $input->getFirstArgument() ?: 'help';

        if (!isset($this->commands[$commandName])) {
            if ($commandName === 'help') {
                $this->renderHelp($output);
                return 0;
            }
            throw new CommandNotFoundException("Command \"{$commandName}\" is not defined.");
        }

        $command = $this->commands[$commandName];

        if ($this->events) {
            $this->events->dispatch(new CommandStarting($commandName, $command));
        }

        $exitCode = $command->run($input, $output);

        if ($this->events) {
            $this->events->dispatch(new CommandFinished($commandName, $command, $exitCode));
        }

        return $exitCode;
    }

    protected function renderHelp(OutputInterface $output): void
    {
        $output->writeln('<info>Palet Framework Console</info>');
        $output->writeln('Available commands:');
        
        foreach ($this->commands as $name => $command) {
            $output->writeln("  <info>{$name}</info>\t" . $command->getDescription());
        }
    }
}
