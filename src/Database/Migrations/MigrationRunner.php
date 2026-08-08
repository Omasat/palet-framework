<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations;

use Palet\Framework\Contracts\Database\Migrations\MigrationRunnerInterface;
use Palet\Framework\Contracts\Database\Migrations\MigrationRepositoryInterface;
use Palet\Framework\Contracts\Database\Migrations\MigrationInterface;
use RuntimeException;

class MigrationRunner implements MigrationRunnerInterface
{
    protected MigrationRepositoryInterface $repository;
    protected MigrationLoader $loader;

    public function __construct(MigrationRepositoryInterface $repository, MigrationLoader $loader)
    {
        $this->repository = $repository;
        $this->loader = $loader;
    }

    public function run(): array
    {
        if (!$this->repository->repositoryExists()) {
            $this->repository->createRepository();
        }
        
        if (!$this->repository->acquireLock()) {
            throw new RuntimeException('Unable to acquire lock. Another migration is running.');
        }

        try {
            $files = $this->loader->getMigrationFiles();
            $ran = $this->repository->getRan();

            $pending = array_diff(array_keys($files), $ran);
            $executed = [];

            if (!empty($pending)) {
                $batch = $this->repository->getNextBatchNumber();
                $this->loader->requireFiles(array_intersect_key($files, array_flip($pending)));

                foreach ($pending as $file) {
                    $this->runUp($file, $files[$file], $batch);
                    $executed[] = $file;
                }
            }
            
            return $executed;
        } finally {
            $this->repository->releaseLock();
        }
    }

    protected function runUp(string $file, string $path, int $batch): void
    {
        $migration = $this->resolve($file);
        $migration->up();
        $this->repository->log($file, $batch);
    }

    public function rollback(): array
    {
        if (!$this->repository->acquireLock()) {
            throw new RuntimeException('Unable to acquire lock. Another migration is running.');
        }
        
        try {
            $migrations = $this->repository->getLast();
            
            if (empty($migrations)) {
                return [];
            }
            
            $files = $this->loader->getMigrationFiles();
            $rolledBack = [];
            
            // Sort in reverse order
            $migrations = array_reverse($migrations);
            
            foreach ($migrations as $migration) {
                $name = $migration['migration'];
                if (isset($files[$name])) {
                    $this->loader->requireFiles([$files[$name]]);
                    $this->runDown($name, $files[$name]);
                    $rolledBack[] = $name;
                }
            }
            
            return $rolledBack;
        } finally {
            $this->repository->releaseLock();
        }
    }
    
    protected function runDown(string $file, string $path): void
    {
        $migration = $this->resolve($file);
        $migration->down();
        $this->repository->delete($file);
    }

    public function reset(): array
    {
        if (!$this->repository->acquireLock()) {
            throw new RuntimeException('Unable to acquire lock. Another migration is running.');
        }
        
        try {
            $ran = $this->repository->getRan();
            
            if (empty($ran)) {
                return [];
            }
            
            $files = $this->loader->getMigrationFiles();
            $rolledBack = [];
            
            $ran = array_reverse($ran);
            
            foreach ($ran as $name) {
                if (isset($files[$name])) {
                    $this->loader->requireFiles([$files[$name]]);
                    $this->runDown($name, $files[$name]);
                    $rolledBack[] = $name;
                }
            }
            
            return $rolledBack;
        } finally {
            $this->repository->releaseLock();
        }
    }

    protected function resolve(string $file): MigrationInterface
    {
        $class = $this->getMigrationClass($file);
        return new $class;
    }

    protected function getMigrationClass(string $migrationName): string
    {
        // 2026_01_01_000000_create_users_table -> CreateUsersTable
        $parts = explode('_', $migrationName);
        $nameParts = array_slice($parts, 4);
        
        return implode('', array_map('ucfirst', $nameParts));
    }
}
