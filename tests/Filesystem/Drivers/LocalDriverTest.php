<?php

declare(strict_types=1);

namespace Tests\Filesystem\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\Drivers\LocalDriver;

class LocalDriverTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = __DIR__ . '/_temp';
        if (!is_dir($this->root)) {
            mkdir($this->root, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        $driver = new LocalDriver($this->root);
        $driver->deleteDirectory('');
        if (is_dir($this->root)) {
            rmdir($this->root);
        }
    }

    public function test_write_and_read()
    {
        $driver = new LocalDriver($this->root);
        
        $this->assertTrue($driver->write('test.txt', 'hello'));
        $this->assertTrue($driver->exists('test.txt'));
        $this->assertEquals('hello', $driver->read('test.txt'));
    }

    public function test_append_and_prepend()
    {
        $driver = new LocalDriver($this->root);
        
        $driver->write('test.txt', 'middle');
        $driver->append('test.txt', ' end');
        $driver->prepend('test.txt', 'start ');
        
        $this->assertEquals('start middle end', $driver->read('test.txt'));
    }

    public function test_delete_and_directory()
    {
        $driver = new LocalDriver($this->root);
        
        $driver->write('folder/file.txt', 'data');
        $this->assertTrue($driver->exists('folder/file.txt'));
        
        $driver->delete('folder/file.txt');
        $this->assertFalse($driver->exists('folder/file.txt'));
        
        $driver->deleteDirectory('folder');
        $this->assertFalse($driver->exists('folder'));
    }

    public function test_stream_read_and_write()
    {
        $driver = new LocalDriver($this->root);
        
        // Write to temp memory stream
        $tempStream = fopen('php://memory', 'rb+');
        fwrite($tempStream, 'stream data');
        rewind($tempStream);
        
        $this->assertTrue($driver->writeStream('stream.txt', $tempStream));
        fclose($tempStream);
        
        $this->assertEquals('stream data', $driver->read('stream.txt'));
        
        // Read stream
        $readStream = $driver->readStream('stream.txt');
        $this->assertIsResource($readStream);
        $this->assertEquals('stream data', stream_get_contents($readStream));
        fclose($readStream);
    }
}
