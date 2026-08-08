<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing;

interface RouteCollectionInterface
{
    public function add(RouteInterface $route): RouteInterface;
    public function refreshNameLookups(): void;
    public function getRoutes(): array;
    public function getRoutesByMethod(): array;
    public function getRoutesByName(): array;
    public function getByName(string $name): ?RouteInterface;
}
