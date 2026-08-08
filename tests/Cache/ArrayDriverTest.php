<?php

declare(strict_types=1);

namespace Tests\Cache;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Cache\Drivers\ArrayDriver;

class ArrayDriverTest extends TestCase
{
    public function test_can_set_and_get()
    {
        $cache = new ArrayDriver();
        $this->assertTrue($cache->set('name', 'John'));
        $this->assertEquals('John', $cache->get('name'));
    }

    public function test_has_returns_true_if_exists()
    {
        $cache = new ArrayDriver();
        $this->assertFalse($cache->has('name'));
        $cache->set('name', 'John');
        $this->assertTrue($cache->has('name'));
    }

    public function test_get_returns_default_if_not_found()
    {
        $cache = new ArrayDriver();
        $this->assertEquals('default', $cache->get('missing', 'default'));
    }

    public function test_delete_removes_item()
    {
        $cache = new ArrayDriver();
        $cache->set('name', 'John');
        $this->assertTrue($cache->delete('name'));
        $this->assertFalse($cache->has('name'));
    }

    public function test_clear_removes_all_items()
    {
        $cache = new ArrayDriver();
        $cache->set('a', 1);
        $cache->set('b', 2);
        $this->assertTrue($cache->clear());
        $this->assertFalse($cache->has('a'));
        $this->assertFalse($cache->has('b'));
    }
}
