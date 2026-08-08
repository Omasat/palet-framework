<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing\Dispatcher;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Routing\Matching\RouteMatch;

interface ControllerDispatcherInterface
{
    /**
     * Dispatch the request to the matched route's action and return a Response.
     */
    public function dispatch(RequestInterface $request, RouteMatch $match): ResponseInterface;
}
