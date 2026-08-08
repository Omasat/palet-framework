<?php

declare(strict_types=1);

namespace Palet\Framework\Routing;

use Palet\Framework\Contracts\Routing\RouteCollectionInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;
use LogicException;

class RouteCollection implements RouteCollectionInterface
{
    /** @var array<int, RouteInterface> */
    protected array $routes = [];

    /** @var array<string, array<string, RouteInterface>> */
    protected array $routesByMethod = [];

    /** @var array<string, RouteInterface> */
    protected array $nameList = [];

    public function add(RouteInterface $route): RouteInterface
    {
        $this->routes[] = $route;
        
        foreach ($route->getMethods() as $method) {
            $this->routesByMethod[$method][$route->getUri()] = $route;
        }
        
        return $route;
    }

    public function refreshNameLookups(): void
    {
        $this->nameList = [];

        foreach ($this->routes as $route) {
            if ($name = $route->getName()) {
                if (isset($this->nameList[$name])) {
                    throw new LogicException("Route named [{$name}] has already been defined.");
                }

                $this->nameList[$name] = $route;
            }
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getRoutesByMethod(): array
    {
        return $this->routesByMethod;
    }

    public function getRoutesByName(): array
    {
        $this->refreshNameLookups();
        return $this->nameList;
    }

    public function getByName(string $name): ?RouteInterface
    {
        $this->refreshNameLookups();
        return $this->nameList[$name] ?? null;
    }
}
