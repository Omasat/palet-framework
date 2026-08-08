<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing\UrlGenerator;

interface UrlGeneratorInterface
{
    /**
     * Get the URL for a given route name.
     */
    public function route(string $name, array $parameters = [], bool $absolute = true): string;

    /**
     * Generate an absolute URL to the given path.
     */
    public function to(string $path, array $extra = [], ?bool $secure = null): string;
    
    /**
     * Generate the URL to an application asset.
     */
    public function asset(string $path, ?bool $secure = null): string;

    /**
     * Get the current URL for the request.
     */
    public function current(): string;

    /**
     * Get the URL for the previous request.
     */
    public function previous(string $fallback = '/'): string;
    
    /**
     * Force the scheme for URLs.
     */
    public function forceScheme(string $scheme): void;

    /**
     * Set the default parameters for URL generation.
     */
    public function defaults(array $defaults): void;
}
