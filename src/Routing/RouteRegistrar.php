<?php

declare(strict_types=1);

namespace Palet\Framework\Routing;

use Palet\Framework\Contracts\Routing\RouteRegistrarInterface;
use Palet\Framework\Contracts\Routing\RouterInterface;
use Closure;
use InvalidArgumentException;

class RouteRegistrar implements RouteRegistrarInterface
{
    protected RouterInterface $router;
    protected array $attributes = [];

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function name(string $name): static
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function middleware(array|string $middleware): static
    {
        $this->attributes['middleware'] = $middleware;
        return $this;
    }

    public function prefix(string $prefix): static
    {
        $this->attributes['prefix'] = $prefix;
        return $this;
    }

    public function domain(string $domain): static
    {
        $this->attributes['domain'] = $domain;
        return $this;
    }

    public function namespace(string $namespace): static
    {
        $this->attributes['namespace'] = $namespace;
        return $this;
    }

    public function group(Closure|string $routes): void
    {
        $this->router->group($this->attributes, $routes);
    }
}
