<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

class MakeEventCommand extends AbstractMakeCommand
{
    protected string $signature = 'make:event {name} {--force} {--dry-run}';
    protected string $description = 'Create a new event class';

    protected function getStub(): string
    {
        return 'event.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Events';
    }
}
