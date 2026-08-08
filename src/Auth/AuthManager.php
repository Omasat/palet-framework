<?php

declare(strict_types=1);

namespace Palet\Framework\Auth;

use Palet\Framework\Contracts\Auth\GuardInterface;
use InvalidArgumentException;
use Closure;

class AuthManager
{
    protected array $guards = [];
    protected array $customCreators = [];
    protected string $defaultGuard = 'web';

    public function guard(?string $name = null): GuardInterface
    {
        $name = $name ?: $this->defaultGuard;

        if (!isset($this->guards[$name])) {
            $this->guards[$name] = $this->resolve($name);
        }

        return $this->guards[$name];
    }

    protected function resolve(string $name): GuardInterface
    {
        if (isset($this->customCreators[$name])) {
            return $this->customCreators[$name]();
        }

        throw new InvalidArgumentException("Auth guard [{$name}] is not defined.");
    }

    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }

    public function setDefaultGuard(string $name): void
    {
        $this->defaultGuard = $name;
    }

    public function __call(string $method, array $parameters)
    {
        return $this->guard()->{$method}(...$parameters);
    }
}
