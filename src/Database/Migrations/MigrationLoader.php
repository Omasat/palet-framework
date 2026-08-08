<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations;

class MigrationLoader
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getMigrationFiles(): array
    {
        if (!is_dir($this->path)) {
            return [];
        }

        $files = glob($this->path . '/*_*.php');
        
        if ($files === false) {
            return [];
        }

        $migrations = [];
        
        foreach ($files as $file) {
            $name = str_replace('.php', '', basename($file));
            $migrations[$name] = $file;
        }

        ksort($migrations);

        return $migrations;
    }

    public function requireFiles(array $files): void
    {
        foreach ($files as $file) {
            require_once $file;
        }
    }
}
