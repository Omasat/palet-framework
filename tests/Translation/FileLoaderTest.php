<?php

declare(strict_types=1);

namespace Tests\Translation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Translation\FileLoader;

class FileLoaderTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/palet_lang_' . uniqid();
        mkdir($this->tempDir);
        mkdir($this->tempDir . '/en');
        mkdir($this->tempDir . '/json');
        
        file_put_contents($this->tempDir . '/en/auth.php', "<?php return ['failed' => 'These credentials do not match our records.'];");
        file_put_contents($this->tempDir . '/json/en.json', json_encode(['Welcome' => 'Welcome to our application.']));
    }

    protected function tearDown(): void
    {
        @unlink($this->tempDir . '/en/auth.php');
        @unlink($this->tempDir . '/json/en.json');
        @rmdir($this->tempDir . '/en');
        @rmdir($this->tempDir . '/json');
        @rmdir($this->tempDir);
    }

    public function test_loads_php_array_file()
    {
        $loader = new FileLoader($this->tempDir);
        $messages = $loader->load('en', 'auth');
        
        $this->assertEquals(['failed' => 'These credentials do not match our records.'], $messages);
    }

    public function test_loads_json_file()
    {
        $loader = new FileLoader($this->tempDir);
        $loader->addJsonPath($this->tempDir . '/json');
        
        $messages = $loader->load('en', '*', '*');
        
        $this->assertArrayHasKey('Welcome', $messages);
        $this->assertEquals('Welcome to our application.', $messages['Welcome']);
    }

    public function test_returns_empty_array_if_file_missing()
    {
        $loader = new FileLoader($this->tempDir);
        $messages = $loader->load('en', 'missing');
        
        $this->assertEquals([], $messages);
    }
}
