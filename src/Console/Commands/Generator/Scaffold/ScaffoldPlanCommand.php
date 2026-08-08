<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator\Scaffold;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Generator\Scaffold\BlueprintRepository;
use Palet\Framework\Generator\Scaffold\DependencyPlanner;
use Palet\Framework\Generator\Scaffold\Blueprints\BasicEntityBlueprint;
use Palet\Framework\Generator\Scaffold\Blueprints\CrudModuleBlueprint;

#[AsCommand('scaffold:plan', 'Preview the execution plan for a blueprint')]
class ScaffoldPlanCommand extends Command
{
    protected function execute(): int
    {
        $blueprintName = $this->argument('1') ?? $this->argument('blueprint');
        
        if (!$blueprintName) {
            $this->error('Blueprint name is required.');
            return 1;
        }

        $repository = new BlueprintRepository();
        $repository->register(new BasicEntityBlueprint());
        $repository->register(new CrudModuleBlueprint());

        $blueprint = $repository->get($blueprintName);
        
        if (!$blueprint) {
            $this->error("Blueprint [{$blueprintName}] not found.");
            return 1;
        }

        $planner = new DependencyPlanner();
        $order = $planner->plan($blueprint->getSteps(), $blueprint->getDependencies());

        $this->info("Execution Plan for [{$blueprintName}]:");
        foreach ($order as $index => $step) {
            $num = $index + 1;
            $this->info("  {$num}. {$step}");
        }

        return 0;
    }
}
