<?php

declare(strict_types=1);

namespace Tests\Session;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Session\Drivers\FileSessionDriver;

class FileSessionDriverTest extends TestCase
{
    protected string $path;
    protected FileSessionDriver $driver;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir() . '/palet_test_sessions';
        $this->driver = new FileSessionDriver($this->path, 1); // 1 min timeout
    }

    protected function tearDown(): void
    {
        $files = glob($this->path . '/*');
        foreach ($files as $file) {
            @unlink($file);
        }
        @rmdir($this->path);
    }

    public function test_write_and_read()
    {
        $this->driver->write('test_id', 'some_data');
        
        $this->assertFileExists($this->path . '/test_id');
        $this->assertEquals('some_data', $this->driver->read('test_id'));
    }

    public function test_destroy_removes_file()
    {
        $this->driver->write('test_id', 'data');
        $this->assertTrue($this->driver->destroy('test_id'));
        $this->assertFileDoesNotExist($this->path . '/test_id');
    }
}
