<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

class MakeNotificationCommand extends AbstractMakeCommand
{
    protected string $signature = 'make:notification {name} {--force} {--dry-run}';
    protected string $description = 'Create a new notification class';

    protected function getStub(): string
    {
        return 'notification.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Notifications';
    }
}
