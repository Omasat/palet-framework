<?php

declare(strict_types=1);

namespace Palet\Framework\Routing;

use Palet\Framework\Contracts\Routing\RouterInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;
use Palet\Framework\Contracts\Routing\RouteCollectionInterface;
use Closure;
use InvalidArgumentException;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Container\ContainerInterface;
use Palet\Framework\Contracts\Routing\Dispatcher\ControllerDispatcherInterface;
use Palet\Framework\Routing\Matching\RouteMatch;
use Palet\Framework\Routing\Matching\RouteMatcher;
use Palet\Framework\Contracts\Routing\Matching\RouteMatcherInterface;
use Palet\Framework\Routing\Exceptions\RouteNotFoundException;
use Palet\Framework\Routing\Exceptions\MethodNotAllowedException;
use Palet\Framework\Http\Message\Response;

class Router implements RouterInterface
{
    protected RouteCollectionInterface $routes;
    protected RouteMatcherInterface $matcher;
    
    /** @var array<int, array> */
    protected array $groupStack = [];
    protected ?ContainerInterface $container = null;

    public function __construct(?RouteCollectionInterface $routes = null, ?ContainerInterface $container = null, ?RouteMatcherInterface $matcher = null)
    {
        $this->routes = $routes ?: new RouteCollection();
        $this->container = $container;
        $this->matcher = $matcher ?: new RouteMatcher();
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function dispatch(RequestInterface $request): ResponseInterface
    {
        try {
            $routeMatch = $this->findRoute($request);
        } catch (RouteNotFoundException $e) {
            return new Response(404, [], 'Not Found');
        } catch (MethodNotAllowedException $e) {
            return new Response(405, ['Allow' => implode(', ', $e->getAllowedMethods())], 'Method Not Allowed');
        }

        if (!$this->container) {
            throw new \RuntimeException('Router needs a Container instance to dispatch routes.');
        }
        
        $dispatcher = $this->container->make(ControllerDispatcherInterface::class);
        return $dispatcher->dispatch($request, $routeMatch);
    }

    protected function findRoute(RequestInterface $request): RouteMatch
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        
        $routesByMethod = $this->routes->getRoutesByMethod();
        
        // Fast static route match (O(1))
        if (isset($routesByMethod[$method][$path])) {
            return new RouteMatch($routesByMethod[$method][$path], []);
        }
        
        // Fallback for dynamic routes using the robust matcher
        return $this->matcher->match($request, $this->routes);
    }

    public function getRoutes(): RouteCollectionInterface
    {
        return $this->routes;
    }

    public function get(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    public function post(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function options(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute('OPTIONS', $uri, $action);
    }

    public function head(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute('HEAD', $uri, $action);
    }

    public function any(string $uri, array|string|callable|null $action = null): RouteInterface
    {
        return $this->addRoute(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $uri, $action);
    }

    public function match(array|string $methods, string $uri, array|string|callable|null $action = null): RouteInterface
    {
        $methods = array_map('strtoupper', (array) $methods);
        $validMethods = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        
        foreach ($methods as $method) {
            if (!in_array($method, $validMethods)) {
                throw new InvalidArgumentException("Invalid HTTP method [{$method}] provided.");
            }
        }
        
        return $this->addRoute($methods, $uri, $action);
    }

    public function prefix(string $prefix): RouteRegistrar
    {
        return (new RouteRegistrar($this))->prefix($prefix);
    }

    public function middleware(array|string $middleware): RouteRegistrar
    {
        return (new RouteRegistrar($this))->middleware($middleware);
    }

    public function name(string $name): RouteRegistrar
    {
        return (new RouteRegistrar($this))->name($name);
    }

    public function group(array $attributes, Closure|string $routes): void
    {
        $this->updateGroupStack($attributes);
        $this->loadRoutes($routes);
        array_pop($this->groupStack);
    }

    protected function addRoute(array|string $methods, string $uri, mixed $action): RouteInterface
    {
        $route = new Route($methods, $uri, $action);

        if ($this->hasGroupStack()) {
            $route->setGroupAttributes($this->getLastGroup());
        }

        return $this->routes->add($route);
    }

    protected function updateGroupStack(array $attributes): void
    {
        if ($this->hasGroupStack()) {
            $attributes = RouteGroup::merge($attributes, $this->getLastGroup());
        }

        $this->groupStack[] = $attributes;
    }

    protected function hasGroupStack(): bool
    {
        return !empty($this->groupStack);
    }

    protected function getLastGroup(): array
    {
        return end($this->groupStack) ?: [];
    }

    protected function loadRoutes(Closure|string $routes): void
    {
        if ($routes instanceof Closure) {
            $routes($this);
        } else {
            $router = $this;
            require $routes;
        }
    }
}
