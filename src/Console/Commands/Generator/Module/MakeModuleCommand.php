<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator\Module;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;
use Palet\Framework\Generator\FileGenerator;
use Palet\Framework\Generator\Module\ModuleGenerator;
use Palet\Framework\Generator\Module\ModuleStructureBuilder;
use Palet\Framework\Generator\Module\ModuleManifestGenerator;
use RuntimeException;

#[AsCommand('make:module', 'Create a new modular architecture component')]
class MakeModuleCommand extends Command
{
    protected function execute(): int
    {
        $name = $this->argument('0') ?? $this->argument('name');
        
        if (!$name) {
            $this->error('Module name is required.');
            return 1;
        }

        $dryRun = $this->option('dry-run') ?? false;

        // Ensure PascalCase
        $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        $basePath = getcwd() . DIRECTORY_SEPARATOR . 'modules';
        
        if (!is_dir($basePath) && !$dryRun) {
            mkdir($basePath, 0755, true);
        }

        $codeGenerator = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );

        $generator = new ModuleGenerator(
            new ModuleStructureBuilder($basePath),
            new ModuleManifestGenerator($codeGenerator),
            $basePath
        );

        try {
            $generator->generate($name, ['dryRun' => $dryRun]);
            
            if ($dryRun) {
                $this->info("DRY RUN: Module structure would be created for {$name}.");
            } else {
                $this->info("Module {$name} created successfully.");
                $this->info("Run 'module:enable {$name}' to activate it.");
            }
            
            return 0;
            
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
