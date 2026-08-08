<?php

declare(strict_types=1);

namespace Tests\Concurrency;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Concurrency\Lock\LockManager;

class LockTest extends TestCase
{
    protected LockManager $manager;
    protected ArrayCacheStore $cache;

    protected function setUp(): void
    {
        $this->cache = new ArrayCacheStore();
        $this->manager = new LockManager($this->cache);
    }

    public function test_lock_acquire_and_release()
    {
        $lock = $this->manager->lock('test_lock', 10);
        
        $this->assertTrue($lock->acquire());
        
        // Secondary acquire should fail since it's already locked
        $this->assertFalse($lock->acquire());
        
        // Release should succeed
        $this->assertTrue($lock->release());
        
        // Can acquire again after release
        $this->assertTrue($lock->acquire());
    }

    public function test_lock_force_release()
    {
        $lock1 = $this->manager->lock('resource_lock', 10);
        $this->assertTrue($lock1->acquire());
        
        $lock2 = $this->manager->lock('resource_lock', 10); // Different owner
        $this->assertFalse($lock2->acquire());
        
        // lock2 cannot release lock1's lock normally
        $this->assertFalse($lock2->release());
        
        // Force release bypasses ownership check
        $lock2->forceRelease();
        $this->assertTrue($lock2->acquire());
    }

    public function test_lock_owner()
    {
        $lock = $this->manager->lock('test_lock', 10, 'custom_owner');
        $this->assertEquals('custom_owner', $lock->owner());
    }
}
