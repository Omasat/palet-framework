<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\StorageManager;
use Palet\Framework\Filesystem\Drivers\LocalDriver;
use Palet\Framework\Filesystem\Drivers\PublicDriver;
use Palet\Framework\Filesystem\Drivers\TemporaryDriver;

class StorageManagerTest extends TestCase
{
    public function test_creates_configured_disks()
    {
        $config = [
            'default' => 'local',
            'disks' => [
                'local' => ['driver' => 'local', 'root' => __DIR__ . '/_temp_local'],
                'public' => ['driver' => 'public', 'root' => __DIR__ . '/_temp_public', 'url' => '/storage'],
                'temp' => ['driver' => 'temporary']
            ]
        ];

        $manager = new StorageManager($config);

        $this->assertInstanceOf(LocalDriver::class, $manager->disk('local'));
        $this->assertInstanceOf(PublicDriver::class, $manager->disk('public'));
        $this->assertInstanceOf(TemporaryDriver::class, $manager->disk('temp'));
        
        // Default disk test
        $this->assertInstanceOf(LocalDriver::class, $manager->disk());
    }

    public function test_magic_methods_proxy_to_default_driver()
    {
        $config = [
            'default' => 'local',
            'disks' => [
                'local' => ['driver' => 'local', 'root' => __DIR__ . '/_temp_local']
            ]
        ];

        $manager = new StorageManager($config);
        
        $manager->write('magic.txt', 'data');
        
        $this->assertTrue($manager->disk('local')->exists('magic.txt'));
        
        $manager->deleteDirectory(''); // Clean up
    }
}
