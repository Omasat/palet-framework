<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator\Module;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Generator\Module\ModuleRegistrar;

#[AsCommand('module:enable', 'Enable a module')]
class ModuleEnableCommand extends Command
{
    protected function execute(): int
    {
        $name = $this->argument('0') ?? $this->argument('name');
        
        if (!$name) {
            $this->error('Module name is required.');
            return 1;
        }

        $statusPath = getcwd() . DIRECTORY_SEPARATOR . 'modules_statuses.json';
        $registrar = new ModuleRegistrar($statusPath);
        
        if ($registrar->enable($name)) {
            $this->info("Module {$name} enabled successfully.");
            return 0;
        }

        $this->error("Failed to enable module {$name}.");
        return 1;
    }
}
