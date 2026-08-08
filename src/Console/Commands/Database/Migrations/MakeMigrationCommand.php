<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Database\Migrations;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Database\Migrations\Generator\MigrationGenerator;
use Palet\Framework\Database\Migrations\Generator\MigrationNameResolver;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;
use Palet\Framework\Generator\FileGenerator;
use RuntimeException;

#[AsCommand('make:migration', 'Create a new migration file')]
class MakeMigrationCommand extends Command
{
    protected function execute(): int
    {
        $name = $this->argument('0') ?? $this->argument('name');
        
        if (!$name) {
            $this->error('Name is required.');
            return 1;
        }

        $create = $this->option('create');
        $table = $this->option('table');
        $dryRun = $this->option('dry-run') ?? false;

        if ($create) {
            $table = $create;
            $isCreate = true;
        } else {
            $isCreate = false;
        }

        // Assuming database/migrations is in root directory of project
        $destinationDir = getcwd() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        
        if (!is_dir($destinationDir) && !$dryRun) {
            mkdir($destinationDir, 0755, true);
        }

        $codeGenerator = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );

        $generator = new MigrationGenerator($codeGenerator, new MigrationNameResolver());

        try {
            $path = $generator->generate($name, $destinationDir, $table, $isCreate, (bool)$dryRun);
            
            if ($dryRun) {
                $this->info("DRY RUN: Would create migration for {$name}");
            } else {
                $this->info("Migration created successfully: " . basename($path));
            }
            
            return 0;
            
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
