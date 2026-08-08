<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Matching;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Routing\Matching\RouteMatcherInterface;
use Palet\Framework\Contracts\Routing\RouteCollectionInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;
use Palet\Framework\Routing\Exceptions\MethodNotAllowedException;
use Palet\Framework\Routing\Exceptions\RouteNotFoundException;

class RouteMatcher implements RouteMatcherInterface
{
    protected UriValidator $uriValidator;
    protected MethodValidator $methodValidator;

    public function __construct()
    {
        $this->uriValidator = new UriValidator();
        $this->methodValidator = new MethodValidator();
    }

    public function match(RequestInterface $request, RouteCollectionInterface $routes): RouteMatch
    {
        $allRoutes = $routes->getRoutes();
        $uriMatchedRoutes = [];

        foreach ($allRoutes as $route) {
            if ($this->uriValidator->matches($route, $request)) {
                if ($this->methodValidator->matches($route, $request)) {
                    $parameters = $this->uriValidator->extractParameters($route, $request);
                    return new RouteMatch($route, $parameters);
                }
                
                $uriMatchedRoutes[] = $route;
            }
        }

        if (count($uriMatchedRoutes) > 0) {
            $allowedMethods = $this->getAllowedMethods($uriMatchedRoutes);
            throw new MethodNotAllowedException($allowedMethods);
        }

        throw new RouteNotFoundException();
    }

    protected function getAllowedMethods(array $routes): array
    {
        $methods = [];
        foreach ($routes as $route) {
            /** @var RouteInterface $route */
            $methods = array_merge($methods, $route->getMethods());
        }
        
        return array_values(array_unique($methods));
    }
}
