<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator\Scaffold;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Generator\Scaffold\BlueprintRepository;
use Palet\Framework\Generator\Scaffold\Blueprints\BasicEntityBlueprint;
use Palet\Framework\Generator\Scaffold\Blueprints\CrudModuleBlueprint;

#[AsCommand('blueprint:list', 'List all available blueprints')]
class BlueprintListCommand extends Command
{
    protected function execute(): int
    {
        $repository = new BlueprintRepository();
        $repository->register(new BasicEntityBlueprint());
        $repository->register(new CrudModuleBlueprint());

        $blueprints = $repository->all();
        
        if (empty($blueprints)) {
            $this->info("No blueprints found.");
            return 0;
        }

        $this->info("Available Blueprints:");
        foreach ($blueprints as $name => $blueprint) {
            $this->info("  - \033[32m{$name}\033[0m : {$blueprint->getDescription()}");
        }

        return 0;
    }
}
