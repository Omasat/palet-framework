<?php

declare(strict_types=1);

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Auth\PasswordHasher;

class PasswordHasherTest extends TestCase
{
    public function test_make_generates_hash()
    {
        $hasher = new PasswordHasher();
        $hash = $hasher->make('password123');
        
        $this->assertNotEquals('password123', $hash);
        $this->assertTrue(password_get_info($hash)['algo'] !== 0);
    }
    
    public function test_check_validates_correct_password()
    {
        $hasher = new PasswordHasher();
        $hash = $hasher->make('password123');
        
        $this->assertTrue($hasher->check('password123', $hash));
        $this->assertFalse($hasher->check('wrongpassword', $hash));
    }
    
    public function test_needs_rehash()
    {
        $hasher = new PasswordHasher();
        // A low cost hash
        $hash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 4]);
        
        // With default cost (usually 10+) it should need rehash
        $this->assertTrue($hasher->needsRehash($hash));
    }
}
