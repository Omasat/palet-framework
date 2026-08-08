<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing\Matching;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;

interface ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     */
    public function matches(RouteInterface $route, RequestInterface $request): bool;
}
