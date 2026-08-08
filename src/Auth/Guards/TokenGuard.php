<?php

declare(strict_types=1);

namespace Palet\Framework\Auth\Guards;

use Palet\Framework\Contracts\Auth\GuardInterface;
use Palet\Framework\Contracts\Auth\UserProviderInterface;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;

class TokenGuard implements GuardInterface
{
    protected UserProviderInterface $provider;
    protected ?AuthenticatableInterface $user = null;
    protected string $token; // Passed manually since Request is not wired yet

    public function __construct(UserProviderInterface $provider, string $token = '')
    {
        $this->provider = $provider;
        $this->token = $token;
    }

    public function user(): ?AuthenticatableInterface
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if (empty($this->token)) {
            return null;
        }

        $this->user = $this->provider->retrieveByCredentials(['api_token' => $this->token]);

        return $this->user;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function id(): mixed
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }
        return null;
    }
    
    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['api_token'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        return $user !== null;
    }
    
    // Test helper to set token
    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->user = null; // reset cached user
    }
}
