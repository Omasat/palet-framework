<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\UrlGenerator;

use Palet\Framework\Contracts\Routing\RouteCollectionInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;
use InvalidArgumentException;

class RouteUrlGenerator
{
    protected RouteCollectionInterface $routes;
    protected UrlDefaults $defaults;

    public function __construct(RouteCollectionInterface $routes, UrlDefaults $defaults)
    {
        $this->routes = $routes;
        $this->defaults = $defaults;
    }

    public function generate(string $name, array $parameters = [], bool $absolute = true, ?string $domain = null, ?string $scheme = null): string
    {
        $route = $this->routes->getByName($name);

        if (!$route) {
            throw new InvalidArgumentException("Route [{$name}] not defined.");
        }

        return $this->toRoute($route, $parameters, $absolute, $domain, $scheme);
    }

    protected function toRoute(RouteInterface $route, array $parameters, bool $absolute, ?string $domain, ?string $scheme): string
    {
        $parameters = array_merge($this->defaults->all(), $parameters);

        $uri = $route->getUri();
        $queryParameters = [];

        // Substitute path parameters
        preg_match_all('/\{([a-zA-Z0-9_]+)(\?)?\}/', $uri, $matches);
        $routeParams = $matches[1] ?? [];
        $isOptional = $matches[2] ?? [];

        foreach ($routeParams as $index => $paramName) {
            if (array_key_exists($paramName, $parameters)) {
                $uri = str_replace($matches[0][$index], (string) $parameters[$paramName], $uri);
                unset($parameters[$paramName]);
            } elseif ($isOptional[$index] === '?') {
                $uri = str_replace('/' . $matches[0][$index], '', $uri);
                $uri = str_replace($matches[0][$index], '', $uri);
            } else {
                throw new InvalidArgumentException("Missing required parameter for [Route: {$route->getName()}] [URI: {$route->getUri()}] [Missing parameter: {$paramName}].");
            }
        }

        // Add remaining parameters as query string
        $queryString = '';
        if (!empty($parameters)) {
            $queryString = '?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        }

        $path = '/' . ltrim($uri, '/');
        
        if (!$absolute) {
            return $path . $queryString;
        }

        $scheme = $scheme ?? 'http';
        $domain = $domain ?? 'localhost'; // Should be from Request in reality

        return "{$scheme}://{$domain}{$path}{$queryString}";
    }
}
