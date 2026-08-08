<?php

declare(strict_types=1);

namespace Tests\Generator\Module;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Module\ModuleStructureBuilder;
use RuntimeException;

class ModuleStructureBuilderTest extends TestCase
{
    protected string $basePath;

    protected function setUp(): void
    {
        $this->basePath = sys_get_temp_dir() . '/palet_modules_' . uniqid();
        mkdir($this->basePath);
    }

    protected function tearDown(): void
    {
        $this->deleteDir($this->basePath);
    }
    
    protected function deleteDir(string $dirPath) {
        if (!is_dir($dirPath)) return;
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function test_builds_module_directory_structure()
    {
        $builder = new ModuleStructureBuilder($this->basePath);
        $builder->build('Invoice');
        
        $modulePath = $this->basePath . DIRECTORY_SEPARATOR . 'Invoice';
        
        $this->assertDirectoryExists($modulePath);
        $this->assertDirectoryExists($modulePath . DIRECTORY_SEPARATOR . 'Domain');
        $this->assertDirectoryExists($modulePath . DIRECTORY_SEPARATOR . 'Application');
        $this->assertDirectoryExists($modulePath . DIRECTORY_SEPARATOR . 'Infrastructure');
        $this->assertDirectoryExists($modulePath . DIRECTORY_SEPARATOR . 'Presentation');
        $this->assertDirectoryExists($modulePath . DIRECTORY_SEPARATOR . 'Config');
    }
    
    public function test_throws_exception_if_module_exists()
    {
        $builder = new ModuleStructureBuilder($this->basePath);
        $builder->build('Invoice');
        
        $this->expectException(RuntimeException::class);
        $builder->build('Invoice');
    }
    
    public function test_dry_run_does_not_create_directories()
    {
        $builder = new ModuleStructureBuilder($this->basePath);
        $builder->build('Invoice', true); // Dry run
        
        $modulePath = $this->basePath . DIRECTORY_SEPARATOR . 'Invoice';
        
        $this->assertDirectoryDoesNotExist($modulePath);
    }
}
