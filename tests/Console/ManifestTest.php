<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Discovery\CommandManifest;
use Palet\Framework\Console\Discovery\CommandScanner;
use Palet\Framework\Console\Discovery\CommandMetadata;

class ManifestTest extends TestCase
{
    protected string $manifestPath;

    protected function setUp(): void
    {
        $this->manifestPath = sys_get_temp_dir() . '/palet_commands_manifest_' . uniqid() . '.php';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->manifestPath)) {
            unlink($this->manifestPath);
        }
    }

    public function test_builds_and_loads_manifest()
    {
        $scanner = new CommandScanner();
        $manifest = new CommandManifest($this->manifestPath);
        
        $manifest->build($scanner, [__DIR__ . '/Fixtures']);
        
        $this->assertFileExists($this->manifestPath);
        
        $loaded = $manifest->load();
        
        $this->assertArrayHasKey('fixture:test', $loaded);
        $this->assertInstanceOf(CommandMetadata::class, $loaded['fixture:test']);
        $this->assertEquals('A test fixture command', $loaded['fixture:test']->description);
    }
}
