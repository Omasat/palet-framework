<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing;

use Closure;

interface RouteRegistrarInterface
{
    public function name(string $name): static;
    public function middleware(array|string $middleware): static;
    public function prefix(string $prefix): static;
    public function domain(string $domain): static;
    public function namespace(string $namespace): static;
    
    public function group(Closure|string $routes): void;
}
