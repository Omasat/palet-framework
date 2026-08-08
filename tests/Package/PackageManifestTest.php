<?php

declare(strict_types=1);

namespace Tests\Package;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Package\PackageManifest;

class PackageManifestTest extends TestCase
{
    protected string $manifestPath;

    protected function setUp(): void
    {
        $this->manifestPath = sys_get_temp_dir() . '/palet_json_' . uniqid() . '.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->manifestPath)) {
            unlink($this->manifestPath);
        }
    }

    public function test_parses_manifest()
    {
        $data = [
            'name' => 'palet/test-package',
            'version' => '1.0.0',
            'dependencies' => [
                'palet/core' => '^1.0.0'
            ]
        ];
        
        file_put_contents($this->manifestPath, json_encode($data));
        
        $manifest = new PackageManifest();
        
        $this->assertEquals('palet/test-package', $manifest->getName($this->manifestPath));
        $this->assertEquals(['palet/core' => '^1.0.0'], $manifest->getDependencies($this->manifestPath));
    }
}
