<?php

declare(strict_types=1);

namespace Palet\Framework\Console;

use Palet\Framework\Contracts\Console\CommandInterface;
use Palet\Framework\Contracts\Console\InputInterface;
use Palet\Framework\Contracts\Console\OutputInterface;

abstract class Command implements CommandInterface
{
    protected string $name = '';
    protected string $description = '';
    protected ?InputInterface $input = null;
    protected ?OutputInterface $output = null;

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        return $this->execute();
    }

    abstract protected function execute(): int;

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    protected function argument(string $name): mixed
    {
        return $this->input->getArgument($name);
    }

    protected function option(string $name): mixed
    {
        return $this->input->getOption($name);
    }

    protected function hasArgument(string $name): bool
    {
        return $this->input->hasArgument($name);
    }

    protected function hasOption(string $name): bool
    {
        return $this->input->hasOption($name);
    }

    protected function info(string $message): void
    {
        $this->output->writeln("<info>{$message}</info>");
    }

    protected function error(string $message): void
    {
        $this->output->writeln("<error>{$message}</error>");
    }

    protected function comment(string $message): void
    {
        $this->output->writeln("<comment>{$message}</comment>");
    }

    protected function line(string $message): void
    {
        $this->output->writeln($message);
    }
}
