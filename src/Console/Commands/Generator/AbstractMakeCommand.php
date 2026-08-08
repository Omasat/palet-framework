<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator;

use Palet\Framework\Console\Command;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\GeneratorContext;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;
use Palet\Framework\Generator\FileGenerator;
use RuntimeException;

abstract class AbstractMakeCommand extends Command
{
    abstract protected function getStub(): string;
    
    abstract protected function getDefaultNamespace(): string;

    protected function execute(): int
    {
        $name = $this->argument('1') ?? $this->argument('name');
        
        if (!$name) {
            $this->error('Name is required.');
            return 1;
        }

        $force = $this->option('force') ?? false;
        $dryRun = $this->option('dry-run') ?? false;

        $stubPath = __DIR__ . '/../../../Generator/Stubs/' . $this->getStub();
        
        // Simple namespace/path resolution
        $nameParts = explode('\\', str_replace('/', '\\', $name));
        $className = array_pop($nameParts);
        
        $namespace = $this->getDefaultNamespace();
        if (!empty($nameParts)) {
            $namespace .= '\\' . implode('\\', $nameParts);
        }

        // Assume app path is in standard location (usually dynamic in full framework)
        $destinationDir = getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 
                          str_replace('App\\', '', str_replace('\\', DIRECTORY_SEPARATOR, $namespace));
        
        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $className . '.php';

        $variables = [
            'Namespace' => $namespace,
            'ClassName' => $className,
        ];

        $generator = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );

        try {
            if ($dryRun) {
                $this->info("DRY RUN: Would create file at {$destinationPath}");
            }
            
            $generator->generate(new GeneratorContext($stubPath, $destinationPath, $variables, (bool)$force, (bool)$dryRun));
            
            if (!$dryRun) {
                $this->info("File created successfully: {$destinationPath}");
            }
            
            return 0;
            
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
