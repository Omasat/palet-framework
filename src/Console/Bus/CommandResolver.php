<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Bus;

use Palet\Framework\Contracts\Console\CommandResolverInterface;
use Palet\Framework\Contracts\Console\CommandInterface;
use Palet\Framework\Console\Discovery\CommandManifest;

class CommandResolver implements CommandResolverInterface
{
    protected array $commands = [];
    protected ?CommandManifest $manifest = null;
    
    public function __construct(?CommandManifest $manifest = null)
    {
        $this->manifest = $manifest;
        if ($this->manifest) {
            $this->commands = $this->manifest->load();
        }
    }

    public function register(string $name, CommandInterface $command): void
    {
        $this->commands[$name] = $command;
    }

    public function resolve(string $commandName): ?CommandInterface
    {
        if (!isset($this->commands[$commandName])) {
            return null;
        }

        $command = $this->commands[$commandName];

        if ($command instanceof CommandInterface) {
            return $command;
        }

        // If it's CommandMetadata (from manifest)
        $class = $command->class;
        if (class_exists($class)) {
            // In a real scenario, this would use a Container for Dependency Injection
            return new $class();
        }

        return null;
    }

    public function getAll(): array
    {
        return $this->commands;
    }
}
