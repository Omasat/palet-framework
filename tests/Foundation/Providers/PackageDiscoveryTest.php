<?php

declare(strict_types=1);

namespace Tests\Foundation\Providers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Providers\PackageDiscovery;

class PackageDiscoveryTest extends TestCase
{
    private string $vendorDir;

    protected function setUp(): void
    {
        $this->vendorDir = __DIR__ . '/_vendor';
        $composerDir = $this->vendorDir . '/composer';
        
        if (!is_dir($composerDir)) {
            mkdir($composerDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        $file = $this->vendorDir . '/composer/installed.json';
        if (file_exists($file)) {
            unlink($file);
        }
        if (is_dir($this->vendorDir . '/composer')) {
            rmdir($this->vendorDir . '/composer');
        }
        if (is_dir($this->vendorDir)) {
            rmdir($this->vendorDir);
        }
    }

    public function test_discovers_packages_with_palet_extra()
    {
        $installedJson = [
            'packages' => [
                [
                    'name' => 'vendor/package1',
                    'extra' => [
                        'palet' => [
                            'providers' => ['Vendor\Package1\ServiceProvider'],
                        ]
                    ]
                ],
                [
                    'name' => 'vendor/package2',
                    // No 'palet' extra node
                ]
            ]
        ];

        file_put_contents($this->vendorDir . '/composer/installed.json', json_encode($installedJson));

        $discovery = new PackageDiscovery($this->vendorDir);
        $packages = $discovery->discover();

        $this->assertCount(1, $packages);
        $this->assertArrayHasKey('vendor/package1', $packages);
        $this->assertEquals(['Vendor\Package1\ServiceProvider'], $packages['vendor/package1']['providers']);
    }

    public function test_returns_empty_if_no_installed_json()
    {
        $discovery = new PackageDiscovery($this->vendorDir);
        
        $this->assertEmpty($discovery->discover());
    }
}
