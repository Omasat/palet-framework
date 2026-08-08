<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Scaffold;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Scaffold\ProjectCreator;
use Palet\Framework\Scaffold\ProjectValidator;
use Palet\Framework\Scaffold\DirectoryStructureBuilder;
use Palet\Framework\Scaffold\EnvironmentInitializer;
use Palet\Framework\Scaffold\ApplicationBootstrapper;
use InvalidArgumentException;
use RuntimeException;

#[AsCommand('create-project', 'Create a new Palet Framework project')]
class CreateProjectCommand extends Command
{
    protected function execute(): int
    {
        $targetPath = $this->argument('0') ?? $this->argument('path');
        $template = $this->option('template') ?? 'web';

        if (!$targetPath) {
            $this->error('Project path is required. Usage: palet create-project <path> [--template=web|api]');
            return 1;
        }

        $this->info("Creating new Palet project at [{$targetPath}] using template [{$template}]...");

        try {
            $creator = new ProjectCreator(
                new ProjectValidator(),
                new DirectoryStructureBuilder(),
                new EnvironmentInitializer(),
                new ApplicationBootstrapper()
            );

            $creator->create($targetPath, $template);

            $this->info("Project created successfully!");
            $this->line("To get started:");
            $this->line("  cd {$targetPath}");
            $this->line("  php palet serve");

            return 0;

        } catch (InvalidArgumentException $e) {
            $this->error("Validation Error: " . $e->getMessage());
            return 1;
        } catch (RuntimeException $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        } catch (\Throwable $e) {
            $this->error("Unexpected Error: " . $e->getMessage());
            return 1;
        }
    }
}
