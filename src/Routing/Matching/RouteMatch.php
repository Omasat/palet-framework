<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Matching;

use Palet\Framework\Contracts\Routing\RouteInterface;

final readonly class RouteMatch
{
    public RouteInterface $route;
    public array $parameters;

    public function __construct(RouteInterface $route, array $parameters = [])
    {
        $this->route = $route;
        $this->parameters = $parameters;
    }
}
