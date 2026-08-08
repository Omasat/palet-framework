<?php

declare(strict_types=1);

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Auth\Providers\MemoryUserProvider;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;

class MockUser implements AuthenticatableInterface
{
    public int $id;
    public string $email;
    public string $password;
    public string $api_token;

    public function __construct(int $id, string $email, string $password, string $api_token = '')
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->api_token = $api_token;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }
}

class MemoryUserProviderTest extends TestCase
{
    protected MemoryUserProvider $provider;
    protected MockUser $user;

    protected function setUp(): void
    {
        $this->user = new MockUser(1, 'test@example.com', password_hash('secret', PASSWORD_DEFAULT), 'token123');
        $this->provider = new MemoryUserProvider([$this->user]);
    }

    public function test_retrieve_by_id()
    {
        $this->assertSame($this->user, $this->provider->retrieveById(1));
        $this->assertNull($this->provider->retrieveById(2));
    }

    public function test_retrieve_by_credentials()
    {
        $user = $this->provider->retrieveByCredentials(['email' => 'test@example.com']);
        $this->assertSame($this->user, $user);

        $this->assertNull($this->provider->retrieveByCredentials(['email' => 'wrong@example.com']));
    }

    public function test_validate_credentials()
    {
        $this->assertTrue($this->provider->validateCredentials($this->user, ['password' => 'secret']));
        $this->assertFalse($this->provider->validateCredentials($this->user, ['password' => 'wrong']));
    }
}
