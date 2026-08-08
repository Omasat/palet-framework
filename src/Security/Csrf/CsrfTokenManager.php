<?php

declare(strict_types=1);

namespace Palet\Framework\Security\Csrf;

use Palet\Framework\Contracts\Security\CsrfManagerInterface;
use Palet\Framework\Contracts\Session\SessionInterface;

class CsrfTokenManager implements CsrfManagerInterface
{
    protected ?SessionInterface $session = null;

    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    public function token(): string
    {
        if ($this->session === null) {
            throw new \RuntimeException('Session store not set.');
        }

        if (!$this->session->has('_token')) {
            $this->session->regenerateToken();
        }

        return $this->session->get('_token');
    }

    public function validate(string $token): bool
    {
        if ($this->session === null) {
            return false;
        }

        $sessionToken = $this->session->get('_token');

        if (!is_string($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}
