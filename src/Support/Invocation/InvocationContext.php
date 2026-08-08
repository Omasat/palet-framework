<?php

declare(strict_types=1);

namespace Palet\Framework\Support\Invocation;

final readonly class InvocationContext
{
    public array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getParameter(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    public function hasParameter(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }
}
