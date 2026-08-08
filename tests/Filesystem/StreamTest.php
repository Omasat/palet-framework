<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\Drivers\MemoryDriver;
use Palet\Framework\Filesystem\Drivers\LocalDriver;

class StreamTest extends TestCase
{
    public function test_memory_driver_streams()
    {
        $driver = new MemoryDriver();
        
        // Write via stream
        $writeStream = fopen('php://memory', 'rb+');
        fwrite($writeStream, 'stream data');
        rewind($writeStream);
        
        $driver->writeStream('test.txt', $writeStream);
        fclose($writeStream);
        
        $this->assertEquals('stream data', $driver->read('test.txt'));
        
        // Read via stream
        $readStream = $driver->readStream('test.txt');
        $this->assertIsResource($readStream);
        $this->assertEquals('stream data', stream_get_contents($readStream));
        fclose($readStream);
    }

    public function test_local_driver_streams()
    {
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'palet_stream_test_' . uniqid();
        mkdir($tempDir, 0777, true);
        
        $driver = new LocalDriver($tempDir);
        
        $writeStream = fopen('php://memory', 'rb+');
        fwrite($writeStream, 'large file mock');
        rewind($writeStream);
        
        $driver->writeStream('large.txt', $writeStream);
        fclose($writeStream);
        
        $readStream = $driver->readStream('large.txt');
        $this->assertIsResource($readStream);
        $this->assertEquals('large file mock', stream_get_contents($readStream));
        fclose($readStream);
        
        $driver->deleteDirectory('');
        @rmdir($tempDir);
    }
}
