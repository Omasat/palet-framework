<?php

declare(strict_types=1);

namespace Tests\Foundation\Providers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Providers\PackageManifest;

class PackageManifestTest extends TestCase
{
    private string $manifestPath;

    protected function setUp(): void
    {
        $this->manifestPath = __DIR__ . '/_temp_package_manifest.php';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->manifestPath)) {
            unlink($this->manifestPath);
        }
    }

    public function test_writes_and_loads_manifest()
    {
        $manifestObj = new PackageManifest($this->manifestPath);

        $data = [
            'vendor/package1' => [
                'providers' => ['Vendor\Package1\ServiceProvider'],
            ]
        ];

        $manifestObj->write($data);
        
        $this->assertTrue($manifestObj->exists());
        
        $loaded = $manifestObj->load();
        
        $this->assertEquals($data, $loaded);
    }
}
