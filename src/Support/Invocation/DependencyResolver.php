<?php

declare(strict_types=1);

namespace Palet\Framework\Support\Invocation;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class DependencyResolver
{
    protected ApplicationInterface $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function resolve(string $class): mixed
    {
        return $this->app->make($class);
    }
    
    public function has(string $class): bool
    {
        // Simple check. If it's a bound interface or existing class.
        return $this->app->has($class) || class_exists($class) || interface_exists($class);
    }
}
