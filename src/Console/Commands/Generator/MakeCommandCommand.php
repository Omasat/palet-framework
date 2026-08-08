<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

class MakeCommandCommand extends AbstractMakeCommand
{
    protected string $signature = 'make:command {name} {--force} {--dry-run}';
    protected string $description = 'Create a new console command class';

    protected function getStub(): string
    {
        return 'command.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Console\\Commands';
    }
}
