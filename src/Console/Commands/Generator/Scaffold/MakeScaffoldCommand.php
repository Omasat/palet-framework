<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator\Scaffold;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Generator\Scaffold\ScaffoldOrchestrator;
use Palet\Framework\Generator\Scaffold\BlueprintRepository;
use Palet\Framework\Generator\Scaffold\BlueprintValidator;
use Palet\Framework\Generator\Scaffold\DependencyPlanner;
use Palet\Framework\Generator\Scaffold\GenerationManifest;
use Palet\Framework\Generator\Scaffold\Blueprints\BasicEntityBlueprint;
use Palet\Framework\Generator\Scaffold\Blueprints\CrudModuleBlueprint;
use RuntimeException;

#[AsCommand('make:scaffold', 'Execute a blueprint to scaffold application components')]
class MakeScaffoldCommand extends Command
{
    protected function execute(): int
    {
        $blueprintName = $this->argument('1') ?? $this->argument('blueprint');
        
        if (!$blueprintName) {
            $this->error('Blueprint name is required.');
            return 1;
        }

        $dryRun = $this->option('dry-run') ?? false;
        
        $repository = new BlueprintRepository();
        $repository->register(new BasicEntityBlueprint());
        $repository->register(new CrudModuleBlueprint());

        $manifestPath = getcwd() . DIRECTORY_SEPARATOR . 'scaffold_manifest.json';
        
        $orchestrator = new ScaffoldOrchestrator(
            $repository,
            new BlueprintValidator(),
            new DependencyPlanner(),
            new GenerationManifest($manifestPath)
        );

        try {
            $orchestrator->execute($blueprintName, ['dryRun' => $dryRun]);
            
            if ($dryRun) {
                $this->info("DRY RUN: Scaffold for [{$blueprintName}] completed safely.");
            } else {
                $this->info("Scaffold [{$blueprintName}] generated successfully.");
            }
            
            return 0;
            
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
