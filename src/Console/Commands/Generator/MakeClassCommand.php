<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

use Palet\Framework\Console\Attributes\AsCommand;

#[AsCommand('make:class', 'Create a new basic class')]
class MakeClassCommand extends AbstractMakeCommand
{
    protected function getStub(): string
    {
        return 'class.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Classes';
    }
}
