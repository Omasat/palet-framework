<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

class MakePolicyCommand extends AbstractMakeCommand
{
    protected string $signature = 'make:policy {name} {--force} {--dry-run}';
    protected string $description = 'Create a new policy class';

    protected function getStub(): string
    {
        return 'policy.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Policies';
    }
}
