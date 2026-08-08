<?php

declare(strict_types=1);

namespace Tests\Scaffold;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Scaffold\ProjectCreator;
use Palet\Framework\Scaffold\ProjectValidator;
use Palet\Framework\Scaffold\DirectoryStructureBuilder;
use Palet\Framework\Scaffold\EnvironmentInitializer;
use Palet\Framework\Scaffold\ApplicationBootstrapper;
use Palet\Framework\Events\EventDispatcher;

class ProjectCreatorTest extends TestCase
{
    protected string $targetPath;

    protected function setUp(): void
    {
        $this->targetPath = sys_get_temp_dir() . '/palet_test_creator_' . uniqid();
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

    public function test_creates_full_project()
    {
        $creator = new ProjectCreator(
            new ProjectValidator(),
            new DirectoryStructureBuilder(),
            new EnvironmentInitializer(),
            new ApplicationBootstrapper()
        );

        $events = new EventDispatcher();
        $dispatched = [];
        
        $events->listen(\Palet\Framework\Scaffold\Events\ProjectCreated::class, function() use (&$dispatched) {
            $dispatched[] = 'created';
        });
        
        $creator->setEventDispatcher($events);

        // Act
        $creator->create($this->targetPath, 'web');

        // Assert Events
        $this->assertContains('created', $dispatched);

        // Assert Env
        $this->assertFileExists($this->targetPath . '/.env');
        $this->assertFileExists($this->targetPath . '/.env.example');
        
        $env = file_get_contents($this->targetPath . '/.env');
        $this->assertStringContainsString('APP_KEY=base64:', $env);

        // Assert Bootstrap
        $this->assertFileExists($this->targetPath . '/bootstrap/app.php');
        
        // Assert Directories
        $this->assertDirectoryExists($this->targetPath . '/app/Http/Controllers');
        $this->assertDirectoryExists($this->targetPath . '/tests/Feature');
    }
}
