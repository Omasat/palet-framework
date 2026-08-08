<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing;

use Closure;

interface RouterInterface
{
    public function get(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function post(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function put(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function patch(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function delete(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function options(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function head(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function any(string $uri, array|string|callable|null $action = null): RouteInterface;
    public function match(array|string $methods, string $uri, array|string|callable|null $action = null): RouteInterface;
    
    public function group(array $attributes, Closure|string $routes): void;
    
    public function dispatch(\Palet\Framework\Contracts\Http\Message\RequestInterface $request): \Palet\Framework\Contracts\Http\Message\ResponseInterface;
}
