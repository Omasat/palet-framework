<?php

declare(strict_types=1);

namespace Tests\Environment;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Environment\EnvLoader;
use RuntimeException;

class EnvLoaderTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = __DIR__ . '/_temp';
        if (!is_dir($this->testDir)) {
            mkdir($this->testDir);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testDir . '/.env')) {
            unlink($this->testDir . '/.env');
        }
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    public function test_loads_env_file()
    {
        file_put_contents($this->testDir . '/.env', "APP_NAME=Palet\n");

        $loader = new EnvLoader($this->testDir);
        $repo = $loader->load();

        $this->assertEquals('Palet', $repo->get('APP_NAME'));
    }

    public function test_returns_empty_repository_if_file_not_found()
    {
        $loader = new EnvLoader($this->testDir, '.env.notfound');
        $repo = $loader->load();

        $this->assertEmpty($repo->all());
    }
}
