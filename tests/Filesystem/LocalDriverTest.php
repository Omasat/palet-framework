<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\Drivers\LocalDriver;

class LocalDriverTest extends TestCase
{
    protected string $tempDir;
    protected LocalDriver $driver;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'palet_fs_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
        
        $this->driver = new LocalDriver($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->driver->deleteDirectory('');
        @rmdir($this->tempDir);
    }

    public function test_put_and_get()
    {
        $this->driver->write('hello.txt', 'world');
        
        $this->assertTrue($this->driver->exists('hello.txt'));
        $this->assertEquals('world', $this->driver->read('hello.txt'));
    }

    public function test_delete_and_move()
    {
        $this->driver->write('old.txt', 'data');
        $this->driver->move('old.txt', 'new.txt');
        
        $this->assertFalse($this->driver->exists('old.txt'));
        $this->assertTrue($this->driver->exists('new.txt'));
        $this->assertEquals('data', $this->driver->read('new.txt'));
        
        $this->driver->delete('new.txt');
        $this->assertFalse($this->driver->exists('new.txt'));
    }
}
