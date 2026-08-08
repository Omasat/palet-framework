<?php

declare(strict_types=1);

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Auth\AuthManager;
use Palet\Framework\Contracts\Auth\GuardInterface;

class MockGuard implements GuardInterface
{
    public function check(): bool { return true; }
    public function guest(): bool { return false; }
    public function user(): mixed { return null; }
    public function id(): mixed { return 1; }
    public function validate(array $credentials = []): bool { return true; }
}

class AuthManagerTest extends TestCase
{
    public function test_resolves_custom_guards()
    {
        $manager = new AuthManager();
        
        $manager->extend('custom', function() {
            return new MockGuard();
        });
        
        $guard = $manager->guard('custom');
        
        $this->assertInstanceOf(MockGuard::class, $guard);
        $this->assertTrue($guard->check());
    }
    
    public function test_default_guard_is_used()
    {
        $manager = new AuthManager();
        $manager->setDefaultGuard('custom');
        $manager->extend('custom', function() {
            return new MockGuard();
        });
        
        // This implicitly calls guard()->check()
        $this->assertTrue($manager->check());
    }
}
