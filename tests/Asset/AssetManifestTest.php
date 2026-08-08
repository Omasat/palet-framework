<?php

declare(strict_types=1);

namespace Tests\Asset;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Asset\AssetManifest;

class AssetManifestTest extends TestCase
{
    protected string $tempDir;
    protected string $manifestFile;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/palet_test_' . uniqid();
        mkdir($this->tempDir);
        $this->manifestFile = $this->tempDir . '/manifest.json';
        
        $manifestData = [
            'resources/js/app.js' => [
                'file' => 'assets/app-123456.js',
                'css' => ['assets/app-123456.css'],
                'isEntry' => true
            ]
        ];
        
        file_put_contents($this->manifestFile, json_encode($manifestData));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->manifestFile)) {
            unlink($this->manifestFile);
        }
        rmdir($this->tempDir);
    }

    public function test_resolves_asset_chunk()
    {
        $manifest = new AssetManifest($this->manifestFile);
        $chunk = $manifest->get('resources/js/app.js');
        
        $this->assertNotNull($chunk);
        $this->assertEquals('assets/app-123456.js', $chunk['file']);
        $this->assertEquals(['assets/app-123456.css'], $chunk['css']);
    }

    public function test_returns_null_for_missing_asset()
    {
        $manifest = new AssetManifest($this->manifestFile);
        $this->assertNull($manifest->get('missing.js'));
    }
}
