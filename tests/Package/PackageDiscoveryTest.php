<?php

declare(strict_types=1);

namespace Tests\Package;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Package\PackageDiscovery;

class PackageDiscoveryTest extends TestCase
{
    protected string $packagePath;

    protected function setUp(): void
    {
        $this->packagePath = sys_get_temp_dir() . '/palet_package_discover_' . uniqid();
        mkdir($this->packagePath);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->packagePath . '/palet.json')) {
            unlink($this->packagePath . '/palet.json');
        }
        rmdir($this->packagePath);
    }

    public function test_discovers_package_components()
    {
        $manifest = [
            'name' => 'palet/test',
            'extra' => [
                'palet' => [
                    'providers' => ['Test\\ServiceProvider'],
                    'commands' => ['Test\\TestCommand']
                ]
            ]
        ];
        
        file_put_contents($this->packagePath . '/palet.json', json_encode($manifest));
        
        $discovery = new PackageDiscovery();
        $discovered = $discovery->discover($this->packagePath);
        
        $this->assertEquals(['Test\\ServiceProvider'], $discovered['providers']);
        $this->assertEquals(['Test\\TestCommand'], $discovered['commands']);
    }
}
