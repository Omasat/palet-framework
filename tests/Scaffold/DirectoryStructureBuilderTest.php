<?php

declare(strict_types=1);

namespace Tests\Scaffold;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Scaffold\DirectoryStructureBuilder;
use Palet\Framework\Scaffold\Templates\WebTemplate;

class DirectoryStructureBuilderTest extends TestCase
{
    protected string $targetPath;

    protected function setUp(): void
    {
        $this->targetPath = sys_get_temp_dir() . '/palet_test_build_' . uniqid();
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->targetPath);
    }

    protected function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function test_builds_directory_structure()
    {
        $builder = new DirectoryStructureBuilder();
        $builder->build($this->targetPath, new WebTemplate());

        $this->assertDirectoryExists($this->targetPath . '/app');
        $this->assertDirectoryExists($this->targetPath . '/config');
        $this->assertDirectoryExists($this->targetPath . '/routes');
        $this->assertDirectoryExists($this->targetPath . '/public');
        
        $this->assertFileExists($this->targetPath . '/app/.gitkeep');
        $this->assertFileExists($this->targetPath . '/public/index.php');
        $this->assertFileExists($this->targetPath . '/routes/web.php');
    }
}
