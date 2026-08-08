<?php

declare(strict_types=1);

namespace Tests\Concurrency;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Concurrency\RateLimiter\RateLimiter;

class RateLimiterTest extends TestCase
{
    protected RateLimiter $limiter;
    protected ArrayCacheStore $cache;

    protected function setUp(): void
    {
        $this->cache = new ArrayCacheStore();
        $this->limiter = new RateLimiter($this->cache);
    }

    public function test_too_many_attempts()
    {
        $key = 'api_user_1';
        $maxAttempts = 3;

        $this->assertFalse($this->limiter->tooManyAttempts($key, $maxAttempts));
        
        $this->limiter->hit($key);
        $this->assertFalse($this->limiter->tooManyAttempts($key, $maxAttempts));
        
        $this->limiter->hit($key);
        $this->assertFalse($this->limiter->tooManyAttempts($key, $maxAttempts));
        
        $this->limiter->hit($key);
        $this->assertTrue($this->limiter->tooManyAttempts($key, $maxAttempts)); // 3 attempts made, equals maxAttempts
    }

    public function test_remaining_attempts()
    {
        $key = 'api_user_2';
        
        $this->assertEquals(5, $this->limiter->remaining($key, 5));
        
        $this->limiter->hit($key);
        $this->assertEquals(4, $this->limiter->remaining($key, 5));
    }

    public function test_clear_resets_attempts()
    {
        $key = 'api_user_3';
        
        $this->limiter->hit($key);
        $this->limiter->hit($key);
        
        $this->assertEquals(0, $this->limiter->remaining($key, 2));
        
        $this->limiter->clear($key);
        
        $this->assertEquals(2, $this->limiter->remaining($key, 2));
    }
}
