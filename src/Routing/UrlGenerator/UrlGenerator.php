<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\UrlGenerator;

use Palet\Framework\Contracts\Routing\UrlGenerator\UrlGeneratorInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    protected RouteUrlGenerator $routeGenerator;
    protected UrlDefaults $defaults;
    protected RequestInterface $request;
    protected ?string $forcedScheme = null;

    public function __construct(RouteUrlGenerator $routeGenerator, UrlDefaults $defaults, RequestInterface $request)
    {
        $this->routeGenerator = $routeGenerator;
        $this->defaults = $defaults;
        $this->request = $request;
    }

    public function route(string $name, array $parameters = [], bool $absolute = true): string
    {
        return $this->routeGenerator->generate(
            $name, 
            $parameters, 
            $absolute, 
            $this->request->getUri()->getHost(), 
            $this->forcedScheme ?? $this->request->getUri()->getScheme()
        );
    }

    public function to(string $path, array $extra = [], ?bool $secure = null): string
    {
        $scheme = $secure === true ? 'https' : ($this->forcedScheme ?? $this->request->getUri()->getScheme());
        $host = $this->request->getUri()->getHost();
        $path = '/' . ltrim($path, '/');
        
        $queryString = '';
        if (!empty($extra)) {
            $queryString = '?' . http_build_query($extra, '', '&', PHP_QUERY_RFC3986);
        }

        return "{$scheme}://{$host}{$path}{$queryString}";
    }

    public function asset(string $path, ?bool $secure = null): string
    {
        return $this->to($path, [], $secure);
    }

    public function current(): string
    {
        return (string) $this->request->getUri();
    }

    public function previous(string $fallback = '/'): string
    {
        $referer = $this->request->getHeaderLine('Referer');
        return $referer ?: $this->to($fallback);
    }

    public function forceScheme(string $scheme): void
    {
        $this->forcedScheme = $scheme;
    }

    public function defaults(array $defaults): void
    {
        foreach ($defaults as $key => $value) {
            $this->defaults->add($key, $value);
        }
    }
}
