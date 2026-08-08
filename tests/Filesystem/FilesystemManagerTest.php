<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\StorageManager;
use Palet\Framework\Filesystem\Drivers\MemoryDriver;

class FilesystemManagerTest extends TestCase
{
    public function test_resolves_disks()
    {
        $config = [
            'default' => 'test_mem',
            'disks' => [
                'test_mem' => [
                    'driver' => 'memory'
                ]
            ]
        ];
        
        $manager = new StorageManager($config);
        
        $disk = $manager->disk();
        
        $this->assertInstanceOf(MemoryDriver::class, $disk);
        
        // Test __call proxy
        $manager->write('proxy.txt', 'data');
        $this->assertTrue($disk->exists('proxy.txt'));
        $this->assertEquals('data', $disk->read('proxy.txt'));
    }
}
