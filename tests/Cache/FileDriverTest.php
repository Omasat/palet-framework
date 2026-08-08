<?php

declare(strict_types=1);

namespace Tests\Cache;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Cache\Drivers\FileDriver;

class FileDriverTest extends TestCase
{
    protected string $cacheDir;

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . '/palet_cache_test_' . uniqid();
    }

    protected function tearDown(): void
    {
        if (is_dir($this->cacheDir)) {
            $files = glob($this->cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            rmdir($this->cacheDir);
        }
    }

    public function test_can_set_and_get()
    {
        $cache = new FileDriver($this->cacheDir);
        $this->assertTrue($cache->set('name', 'John'));
        $this->assertEquals('John', $cache->get('name'));
    }
    
    public function test_stores_complex_data()
    {
        $cache = new FileDriver($this->cacheDir);
        $data = ['id' => 1, 'active' => true];
        $this->assertTrue($cache->set('user', $data));
        $this->assertEquals($data, $cache->get('user'));
    }

    public function test_delete_removes_file()
    {
        $cache = new FileDriver($this->cacheDir);
        $cache->set('name', 'John');
        $this->assertTrue($cache->has('name'));
        $this->assertTrue($cache->delete('name'));
        $this->assertFalse($cache->has('name'));
    }
    
    public function test_ttl_expires_data()
    {
        $cache = new FileDriver($this->cacheDir);
        
        // TTL is negative, meaning it already expired
        $cache->set('temp', 'value', -10);
        
        $this->assertFalse($cache->has('temp'));
        $this->assertNull($cache->get('temp'));
    }
}
