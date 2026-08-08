<?php

declare(strict_types=1);

namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Config\ConfigLoader;

class ConfigLoaderTest extends TestCase
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
        $files = glob($this->testDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    public function test_loads_php_files()
    {
        file_put_contents($this->testDir . '/app.php', "<?php return ['name' => 'Palet'];");
        file_put_contents($this->testDir . '/database.php', "<?php return ['default' => 'mysql'];");
        
        // This should be ignored
        file_put_contents($this->testDir . '/ignored.txt', "Not a PHP file");

        $loader = new ConfigLoader();
        $items = $loader->load($this->testDir);

        $this->assertCount(2, $items);
        $this->assertEquals('Palet', $items['app']['name']);
        $this->assertEquals('mysql', $items['database']['default']);
        $this->assertArrayNotHasKey('ignored', $items);
    }

    public function test_returns_empty_array_if_path_invalid()
    {
        $loader = new ConfigLoader();
        $items = $loader->load('/invalid/path/that/does/not/exist');

        $this->assertEmpty($items);
    }
}
