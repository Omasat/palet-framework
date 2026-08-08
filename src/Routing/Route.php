<?php

declare(strict_types=1);

namespace Palet\Framework\Routing;

use Palet\Framework\Contracts\Routing\RouteInterface;
use InvalidArgumentException;

class Route implements RouteInterface
{
    protected string $uri;
    protected array $methods;
    protected mixed $action;
    
    protected ?string $name = null;
    protected array $middleware = [];
    protected array $wheres = [];
    protected string $prefix = '';
    protected string $domain = '';

    public function __construct(array|string $methods, string $uri, mixed $action)
    {
        $this->uri = $uri;
        $this->methods = (array) $methods;
        $this->action = $action;

        if (in_array('GET', $this->methods) && !in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getAction(): mixed
    {
        return $this->action;
    }

    public function getWheres(): array
    {
        return $this->wheres;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function name(string $name): static
    {
        $this->name = isset($this->name) ? $this->name . $name : $name;
        return $this;
    }

    public function middleware(array|string $middleware): static
    {
        $this->middleware = array_merge($this->middleware, (array) $middleware);
        return $this;
    }

    public function where(array|string $name, ?string $expression = null): static
    {
        if (is_array($name)) {
            $this->wheres = array_merge($this->wheres, $name);
        } else {
            $this->wheres[$name] = $expression;
        }
        return $this;
    }

    public function prefix(string $prefix): static
    {
        $prefix = trim($prefix, '/');
        $this->uri = trim($this->uri, '/');
        
        $this->uri = $prefix === '' ? $this->uri : $prefix . ($this->uri === '' ? '' : '/' . $this->uri);
        $this->uri = $this->uri === '' ? '/' : '/' . $this->uri;
        
        return $this;
    }

    public function domain(string $domain): static
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Set the attributes from a route group.
     */
    public function setGroupAttributes(array $attributes): static
    {
        if (isset($attributes['prefix'])) {
            $this->prefix($attributes['prefix']);
        }

        if (isset($attributes['middleware'])) {
            $this->middleware($attributes['middleware']);
        }

        if (isset($attributes['name'])) {
            $this->name($attributes['name']);
        }

        if (isset($attributes['domain'])) {
            $this->domain($attributes['domain']);
        }
        
        if (isset($attributes['where'])) {
            $this->where($attributes['where']);
        }

        return $this;
    }
}
