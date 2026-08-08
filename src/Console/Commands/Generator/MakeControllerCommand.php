<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

use Palet\Framework\Console\Attributes\AsCommand;

#[AsCommand(name: 'make:controller', description: 'Create a new controller class')]
class MakeControllerCommand extends AbstractMakeCommand
{
    protected string $name = 'make:controller';
    protected string $description = 'Create a new controller class';
    protected function getStub(): string
    {
        return 'controller.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Http\\Controllers';
    }
}
