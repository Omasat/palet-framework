<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

use Palet\Framework\Console\Attributes\AsCommand;

#[AsCommand('make:middleware', 'Create a new middleware class')]
class MakeMiddlewareCommand extends AbstractMakeCommand
{
    protected function getStub(): string
    {
        return 'middleware.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Http\\Middleware';
    }
}
