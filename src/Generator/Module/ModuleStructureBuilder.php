<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Module;

use RuntimeException;

class ModuleStructureBuilder
{
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function build(string $moduleName, bool $dryRun = false): void
    {
        $directories = [
            'Domain/Entities',
            'Domain/ValueObjects',
            'Domain/Repositories',
            'Application/Services',
            'Application/DTOs',
            'Infrastructure/Persistence',
            'Presentation/Controllers',
            'Presentation/Requests',
            'Presentation/Resources',
            'Presentation/Views',
            'Contracts',
            'Routes',
            'Config',
            'Database/Migrations',
            'Database/Seeders',
            'Tests',
            'Docs'
        ];

        $modulePath = $this->basePath . DIRECTORY_SEPARATOR . $moduleName;

        if (is_dir($modulePath) && !$dryRun) {
            throw new RuntimeException("Module {$moduleName} already exists.");
        }

        foreach ($directories as $dir) {
            $path = $modulePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dir);
            if (!$dryRun) {
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
            }
        }
    }
}
