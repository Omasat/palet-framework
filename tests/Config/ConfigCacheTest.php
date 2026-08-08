<?php

declare(strict_types=1);

namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Config\ConfigCache;

class ConfigCacheTest extends TestCase
{
    private string $cacheFile;

    protected function setUp(): void
    {
        $this->cacheFile = __DIR__ . '/_temp_config_cache.php';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    public function test_writes_and_reads_cache_file()
    {
        $config = [
            'app' => ['name' => 'Palet'],
            'database' => ['default' => 'mysql']
        ];

        // Write
        $result = ConfigCache::write($this->cacheFile, $config);
        $this->assertTrue($result);
        $this->assertFileExists($this->cacheFile);

        // Read
        $readConfig = ConfigCache::read($this->cacheFile);
        
        $this->assertIsArray($readConfig);
        $this->assertEquals('Palet', $readConfig['app']['name']);
        $this->assertEquals('mysql', $readConfig['database']['default']);
    }

    public function test_read_returns_null_if_file_not_found()
    {
        $readConfig = ConfigCache::read($this->cacheFile);
        
        $this->assertNull($readConfig);
    }
}
