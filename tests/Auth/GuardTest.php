<?php

declare(strict_types=1);

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Auth\Guards\TokenGuard;
use Palet\Framework\Auth\Guards\SessionGuard;
use Palet\Framework\Auth\Providers\MemoryUserProvider;

class TokenGuardTest extends TestCase
{
    protected TokenGuard $guard;
    protected MockUser $user;

    protected function setUp(): void
    {
        $this->user = new MockUser(1, 'test@example.com', '', 'valid-token');
        $provider = new MemoryUserProvider([$this->user]);
        $this->guard = new TokenGuard($provider);
    }

    public function test_authenticates_with_valid_token()
    {
        $this->guard->setToken('valid-token');
        
        $this->assertTrue($this->guard->check());
        $this->assertFalse($this->guard->guest());
        $this->assertSame($this->user, $this->guard->user());
        $this->assertEquals(1, $this->guard->id());
    }

    public function test_fails_with_invalid_token()
    {
        $this->guard->setToken('invalid-token');
        
        $this->assertFalse($this->guard->check());
        $this->assertTrue($this->guard->guest());
        $this->assertNull($this->guard->user());
        $this->assertNull($this->guard->id());
    }
}

class SessionGuardTest extends TestCase
{
    public function test_attempt_and_login()
    {
        $user = new MockUser(1, 'test@example.com', password_hash('secret', PASSWORD_DEFAULT));
        $provider = new MemoryUserProvider([$user]);
        $guard = new SessionGuard($provider);

        $this->assertTrue($guard->attempt(['email' => 'test@example.com', 'password' => 'secret']));
        $this->assertTrue($guard->check());
        $this->assertEquals(1, $guard->id());
        
        $guard->logout();
        $this->assertFalse($guard->check());
    }
}
