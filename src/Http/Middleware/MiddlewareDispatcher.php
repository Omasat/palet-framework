<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;
use Palet\Framework\Pipeline\Pipeline;
use Closure;
use InvalidArgumentException;

class MiddlewareDispatcher
{
    protected ApplicationInterface $app;
    protected MiddlewareRegistry $registry;
    protected MiddlewareResolver $resolver;
    protected MiddlewarePriorityResolver $priorityResolver;

    public function __construct(
        ApplicationInterface $app, 
        MiddlewareRegistry $registry, 
        MiddlewareResolver $resolver,
        MiddlewarePriorityResolver $priorityResolver
    ) {
        $this->app = $app;
        $this->registry = $registry;
        $this->resolver = $resolver;
        $this->priorityResolver = $priorityResolver;
    }

    public function dispatch(RequestInterface $request, ?RouteInterface $route, Closure $destination): ResponseInterface
    {
        $middleware = $this->gatherMiddleware($route);
        $sortedMiddleware = $this->priorityResolver->sort($middleware);

        $pipeline = new Pipeline($this->app);

        return $pipeline->send($request)
            ->through($sortedMiddleware)
            ->then($destination);
    }

    protected function gatherMiddleware(?RouteInterface $route): array
    {
        $middleware = $this->registry->getGlobalMiddleware();

        if ($route) {
            foreach ($route->getMiddleware() ?? [] as $name) {
                if ($this->isGroup($name)) {
                    $middleware = array_merge($middleware, $this->resolver->resolveGroup($name));
                } else {
                    $middleware[] = $this->resolver->resolve($name);
                }
            }
        }

        return $middleware;
    }

    protected function isGroup(string $name): bool
    {
        return isset($this->registry->getGroups()[$name]);
    }
}
