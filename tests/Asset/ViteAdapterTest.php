<?php

declare(strict_types=1);

namespace Tests\Asset;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Asset\Adapters\ViteAdapter;
use Palet\Framework\Asset\DevServerResolver;
use Palet\Framework\Asset\AssetManifest;

class ViteAdapterTest extends TestCase
{
    protected string $tempDir;
    protected string $hotFile;
    protected string $manifestFile;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/palet_test_' . uniqid();
        mkdir($this->tempDir);
        $this->hotFile = $this->tempDir . '/hot';
        $this->manifestFile = $this->tempDir . '/manifest.json';
    }

    protected function tearDown(): void
    {
        @unlink($this->hotFile);
        @unlink($this->manifestFile);
        rmdir($this->tempDir);
    }

    public function test_generates_dev_server_assets()
    {
        file_put_contents($this->hotFile, 'http://localhost:5173');
        
        $resolver = new DevServerResolver($this->hotFile);
        $manifest = new AssetManifest($this->manifestFile);
        $adapter = new ViteAdapter($resolver, $manifest);
        
        $html = $adapter('resources/js/app.js')->toHtml();
        
        $this->assertStringContainsString('<script type="module" src="http://localhost:5173/@vite/client"></script>', $html);
        $this->assertStringContainsString('<script type="module" src="http://localhost:5173/resources/js/app.js"></script>', $html);
    }

    public function test_generates_production_assets()
    {
        $manifestData = [
            'resources/js/app.js' => [
                'file' => 'assets/app-123456.js',
                'css' => ['assets/app-123456.css']
            ]
        ];
        file_put_contents($this->manifestFile, json_encode($manifestData));
        
        $resolver = new DevServerResolver($this->hotFile); // no hot file
        $manifest = new AssetManifest($this->manifestFile);
        $adapter = new ViteAdapter($resolver, $manifest, '/build');
        
        $html = $adapter('resources/js/app.js')->toHtml();
        
        $this->assertStringContainsString('<script type="module" src="/build/assets/app-123456.js"></script>', $html);
        $this->assertStringContainsString('<link rel="stylesheet" href="/build/assets/app-123456.css">', $html);
        $this->assertStringNotContainsString('@vite/client', $html);
    }
}
