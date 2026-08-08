<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Security;

use Palet\Framework\Contracts\Session\SessionInterface;

interface CsrfManagerInterface
{
    /**
     * Get the CSRF token value.
     */
    public function token(): string;

    /**
     * Determine if the given CSRF token is valid.
     */
    public function validate(string $token): bool;

    /**
     * Set the underlying session store.
     */
    public function setSession(SessionInterface $session): void;
}
