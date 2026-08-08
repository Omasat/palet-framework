<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

class MakeJobCommand extends AbstractMakeCommand
{
    protected string $signature = 'make:job {name} {--force} {--dry-run}';
    protected string $description = 'Create a new job class';

    protected function getStub(): string
    {
        return 'job.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Jobs';
    }
}
