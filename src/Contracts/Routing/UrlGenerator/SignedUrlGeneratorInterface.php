<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing\UrlGenerator;

use DateTimeInterface;

interface SignedUrlGeneratorInterface
{
    /**
     * Create a signed route URL for a named route.
     */
    public function signedRoute(string $name, array $parameters = [], ?DateTimeInterface $expiration = null, bool $absolute = true): string;

    /**
     * Create a temporary signed route URL for a named route.
     */
    public function temporarySignedRoute(string $name, DateTimeInterface $expiration, array $parameters = [], bool $absolute = true): string;

    /**
     * Determine if the given request has a valid signature.
     */
    public function hasValidSignature(string $url, array $query): bool;
}
