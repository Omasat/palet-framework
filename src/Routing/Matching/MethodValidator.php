<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Matching;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Routing\Matching\ValidatorInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;

class MethodValidator implements ValidatorInterface
{
    public function matches(RouteInterface $route, RequestInterface $request): bool
    {
        $method = strtoupper($request->getMethod());
        
        return in_array($method, $route->getMethods(), true);
    }
}
