<?php

declare(strict_types=1);

namespace Tests\Cache;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Cache\Drivers\ArrayDriver;
use Palet\Framework\Cache\CacheRepository;

class CacheRepositoryTest extends TestCase
{
    public function test_remember_executes_callback_if_missing()
    {
        $store = new ArrayDriver();
        $repo = new CacheRepository($store);
        
        $executed = false;
        
        $result = $repo->remember('key', 60, function () use (&$executed) {
            $executed = true;
            return 'calculated_value';
        });
        
        $this->assertTrue($executed);
        $this->assertEquals('calculated_value', $result);
        $this->assertEquals('calculated_value', $store->get('key'));
    }

    public function test_remember_does_not_execute_callback_if_exists()
    {
        $store = new ArrayDriver();
        $store->set('key', 'cached_value');
        $repo = new CacheRepository($store);
        
        $executed = false;
        
        $result = $repo->remember('key', 60, function () use (&$executed) {
            $executed = true;
            return 'calculated_value';
        });
        
        $this->assertFalse($executed);
        $this->assertEquals('cached_value', $result);
    }
    
    public function test_pull_returns_and_deletes()
    {
        $store = new ArrayDriver();
        $store->set('key', 'value');
        $repo = new CacheRepository($store);
        
        $result = $repo->pull('key');
        
        $this->assertEquals('value', $result);
        $this->assertFalse($store->has('key'));
    }
}
