<?php

declare(strict_types=1);

namespace Palet\Framework\Auth\Guards;

use Palet\Framework\Contracts\Auth\StatefulGuardInterface;
use Palet\Framework\Contracts\Auth\UserProviderInterface;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;

class SessionGuard implements StatefulGuardInterface
{
    protected UserProviderInterface $provider;
    protected ?AuthenticatableInterface $user = null;

    public function __construct(UserProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function attempt(array $credentials = [], bool $remember = false): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user && $this->provider->validateCredentials($user, $credentials)) {
            $this->login($user, $remember);
            return true;
        }

        return false;
    }

    public function login(AuthenticatableInterface $user, bool $remember = false): void
    {
        $this->user = $user;
        $_SESSION['user_id'] = $user->getAuthIdentifier();
        // Remember me logic will be added here
    }

    public function logout(): void
    {
        $this->user = null;
        unset($_SESSION['user_id']);
    }

    public function user(): ?AuthenticatableInterface
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $id = $_SESSION['user_id'] ?? null;
        if ($id !== null) {
            $this->user = $this->provider->retrieveById($id);
        }

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
        $user = $this->provider->retrieveByCredentials($credentials);
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }
}
