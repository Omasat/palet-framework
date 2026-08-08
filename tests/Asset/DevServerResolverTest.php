<?php

declare(strict_types=1);

namespace Tests\Asset;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Asset\DevServerResolver;

class DevServerResolverTest extends TestCase
{
    protected string $tempDir;
    protected string $hotFile;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/palet_test_' . uniqid();
        mkdir($this->tempDir);
        $this->hotFile = $this->tempDir . '/hot';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->hotFile)) {
            unlink($this->hotFile);
        }
        rmdir($this->tempDir);
    }

    public function test_dev_server_is_running_when_hot_file_exists()
    {
        $resolver = new DevServerResolver($this->hotFile);
        
        $this->assertFalse($resolver->isRunning());
        
        file_put_contents($this->hotFile, 'http://localhost:8080');
        
        $this->assertTrue($resolver->isRunning());
        $this->assertEquals('http://localhost:8080', $resolver->url());
    }

    public function test_dev_server_defaults_to_vite_port()
    {
        file_put_contents($this->hotFile, '');
        $resolver = new DevServerResolver($this->hotFile);
        
        $this->assertTrue($resolver->isRunning());
        $this->assertEquals('http://localhost:5173', $resolver->url());
    }
}
