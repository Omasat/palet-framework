<?php

declare(strict_types=1);

namespace Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Authorization\Gate;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;

class AuthUser implements AuthenticatableInterface
{
    public int $id = 1;
    public bool $is_admin = false;

    public function getAuthIdentifierName(): string { return 'id'; }
    public function getAuthIdentifier(): mixed { return $this->id; }
    public function getAuthPassword(): string { return ''; }
}

class PostPolicy
{
    public function update(AuthUser $user, $post)
    {
        return $user->id === $post->user_id;
    }
}

class GateTest extends TestCase
{
    protected Gate $gate;
    protected AuthUser $user;

    protected function setUp(): void
    {
        $this->user = new AuthUser();
        
        $this->gate = new Gate(function() {
            return $this->user;
        });
    }

    public function test_define_and_allows()
    {
        $this->gate->define('edit-settings', function ($user) {
            return $user->is_admin;
        });

        $this->assertFalse($this->gate->allows('edit-settings'));
        
        $this->user->is_admin = true;
        
        $this->assertTrue($this->gate->allows('edit-settings'));
    }

    public function test_before_hook_can_bypass_rules()
    {
        $this->gate->define('edit-settings', function ($user) {
            return false;
        });

        $this->gate->before(function ($user, $ability) {
            if ($user->is_admin) {
                return true;
            }
        });

        $this->assertFalse($this->gate->allows('edit-settings'));
        
        $this->user->is_admin = true;
        
        $this->assertTrue($this->gate->allows('edit-settings'));
    }

    public function test_policy_resolution()
    {
        $this->gate->policy('stdClass', PostPolicy::class);

        $post = (object) ['user_id' => 1]; // belongs to user 1
        $otherPost = (object) ['user_id' => 2];

        $this->assertTrue($this->gate->allows('update', $post));
        $this->assertFalse($this->gate->allows('update', $otherPost));
    }
}
