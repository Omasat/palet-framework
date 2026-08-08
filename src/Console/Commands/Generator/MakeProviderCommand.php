<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

class MakeProviderCommand extends AbstractMakeCommand
{
    protected string $signature = 'make:provider {name} {--force} {--dry-run}';
    protected string $description = 'Create a new service provider class';

    protected function getStub(): string
    {
        return 'provider.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Providers';
    }
}
