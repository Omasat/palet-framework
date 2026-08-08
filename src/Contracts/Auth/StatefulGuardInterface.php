<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Auth;

interface StatefulGuardInterface extends GuardInterface
{
    /**
     * Attempt to authenticate a user using the given credentials.
     */
    public function attempt(array $credentials = [], bool $remember = false): bool;

    /**
     * Log a user into the application.
     */
    public function login(AuthenticatableInterface $user, bool $remember = false): void;

    /**
     * Log the user out of the application.
     */
    public function logout(): void;
}
