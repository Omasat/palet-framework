<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator\Module;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Generator\Module\ModuleRegistrar;

#[AsCommand('module:list', 'List all modules and their status')]
class ModuleListCommand extends Command
{
    protected function execute(): int
    {
        $statusPath = getcwd() . DIRECTORY_SEPARATOR . 'modules_statuses.json';
        $registrar = new ModuleRegistrar($statusPath);
        
        $modules = $registrar->all();
        
        if (empty($modules)) {
            $this->info("No modules found.");
            return 0;
        }

        foreach ($modules as $name => $active) {
            $status = $active ? "\033[32mEnabled\033[0m" : "\033[31mDisabled\033[0m";
            $this->info("{$name} - [{$status}]");
        }

        return 0;
    }
}
