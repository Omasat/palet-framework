<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing\Matching;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Routing\RouteCollectionInterface;
use Palet\Framework\Routing\Matching\RouteMatch;

interface RouteMatcherInterface
{
    /**
     * Match a given request to a route within the collection.
     *
     * @throws \Palet\Framework\Routing\Exceptions\RouteNotFoundException
     * @throws \Palet\Framework\Routing\Exceptions\MethodNotAllowedException
     */
    public function match(RequestInterface $request, RouteCollectionInterface $routes): RouteMatch;
}
