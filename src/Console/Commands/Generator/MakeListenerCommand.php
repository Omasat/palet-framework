<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

class MakeListenerCommand extends AbstractMakeCommand
{
    protected string $signature = 'make:listener {name} {--force} {--dry-run}';
    protected string $description = 'Create a new event listener class';

    protected function getStub(): string
    {
        return 'listener.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Listeners';
    }
}
