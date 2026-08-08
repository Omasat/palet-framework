<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing;

interface RouteInterface
{
    public function getUri(): string;
    public function getMethods(): array;
    public function getAction(): mixed;
    public function getName(): ?string;
    public function getWheres(): array;
    public function getMiddleware(): array;
    
    public function name(string $name): static;
    public function middleware(array|string $middleware): static;
    public function where(array|string $name, ?string $expression = null): static;
    public function prefix(string $prefix): static;
    public function domain(string $domain): static;
}
