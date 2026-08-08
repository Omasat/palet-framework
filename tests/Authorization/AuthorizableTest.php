<?php

declare(strict_types=1);

namespace Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Authorization\Gate;
use Palet\Framework\Authorization\Traits\Authorizable;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;

class UserWithTrait implements AuthenticatableInterface
{
    use Authorizable;

    public int $id = 1;

    public function getAuthIdentifierName(): string { return 'id'; }
    public function getAuthIdentifier(): mixed { return $this->id; }
    public function getAuthPassword(): string { return ''; }
}

class AuthorizableTest extends TestCase
{
    protected Gate $gate;
    protected UserWithTrait $user;

    protected function setUp(): void
    {
        $this->user = new UserWithTrait();
        
        $this->gate = new Gate(function() {
            return $this->user;
        });
        
        UserWithTrait::setGate($this->gate);
    }

    public function test_can_method_delegates_to_gate()
    {
        $this->gate->define('do-something', function ($user) {
            return true;
        });

        $this->gate->define('do-nothing', function ($user) {
            return false;
        });

        $this->assertTrue($this->user->can('do-something'));
        $this->assertTrue($this->user->cannot('do-nothing'));
    }
}
