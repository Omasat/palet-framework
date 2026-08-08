<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization;

use Palet\Framework\Contracts\Authorization\GateInterface;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;
use Closure;
use InvalidArgumentException;

class Gate implements GateInterface
{
    protected array $policies = [];
    protected array $abilities = [];
    protected array $beforeCallbacks = [];
    protected array $afterCallbacks = [];
    
    /** @var callable */
    protected $userResolver;

    public function __construct(callable $userResolver = null)
    {
        $this->userResolver = $userResolver ?: fn() => null;
    }

    public function define(string $ability, callable|string $callback): static
    {
        $this->abilities[$ability] = $callback;
        return $this;
    }

    public function policy(string $class, string $policy): static
    {
        $this->policies[$class] = $policy;
        return $this;
    }

    public function has(string $ability): bool
    {
        return isset($this->abilities[$ability]);
    }

    public function before(callable $callback): static
    {
        $this->beforeCallbacks[] = $callback;
        return $this;
    }

    public function after(callable $callback): static
    {
        $this->afterCallbacks[] = $callback;
        return $this;
    }

    public function allows(string $ability, mixed $arguments = []): bool
    {
        return $this->inspect($ability, $arguments)->allowed();
    }

    public function denies(string $ability, mixed $arguments = []): bool
    {
        return !$this->allows($ability, $arguments);
    }

    public function check(iterable|string $abilities, mixed $arguments = []): bool
    {
        foreach ((array) $abilities as $ability) {
            if (!$this->allows($ability, $arguments)) {
                return false;
            }
        }
        return true;
    }

    public function any(iterable|string $abilities, mixed $arguments = []): bool
    {
        foreach ((array) $abilities as $ability) {
            if ($this->allows($ability, $arguments)) {
                return true;
            }
        }
        return false;
    }

    public function authorize(string $ability, mixed $arguments = []): Response
    {
        return $this->inspect($ability, $arguments)->authorize();
    }

    public function inspect(string $ability, mixed $arguments = []): Response
    {
        $user = call_user_func($this->userResolver);
        
        $arguments = is_array($arguments) ? $arguments : [$arguments];
        
        $result = $this->callBeforeCallbacks($user, $ability, $arguments);
        
        if ($result === null) {
            $result = $this->resolveAuthCallback($user, $ability, $arguments);
        }
        
        $afterResult = $this->callAfterCallbacks($user, $ability, $arguments, $result);
        if ($afterResult !== null) {
            $result = $afterResult;
        }

        return $this->prepareResponse($result);
    }
    
    protected function callBeforeCallbacks(?AuthenticatableInterface $user, string $ability, array $arguments): mixed
    {
        foreach ($this->beforeCallbacks as $before) {
            $result = $before($user, $ability, $arguments);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }
    
    protected function callAfterCallbacks(?AuthenticatableInterface $user, string $ability, array $arguments, mixed $result): mixed
    {
        foreach ($this->afterCallbacks as $after) {
            $afterResult = $after($user, $ability, $result, $arguments);
            if ($afterResult !== null) {
                return $afterResult;
            }
        }
        return null;
    }
    
    protected function resolveAuthCallback(?AuthenticatableInterface $user, string $ability, array $arguments): mixed
    {
        // 1. Try Ability definitions
        if (isset($this->abilities[$ability])) {
            return call_user_func_array($this->abilities[$ability], array_merge([$user], $arguments));
        }
        
        // 2. Try Policy resolution
        if (count($arguments) > 0) {
            $instance = $arguments[0];
            $class = is_object($instance) ? get_class($instance) : $instance;
            
            if (is_string($class) && isset($this->policies[$class])) {
                $policy = $this->policies[$class];
                if (class_exists($policy)) {
                    $policyInstance = new $policy();
                    if (method_exists($policyInstance, $ability)) {
                        return call_user_func_array([$policyInstance, $ability], array_merge([$user], $arguments));
                    }
                }
            }
        }

        return false;
    }

    protected function prepareResponse(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        return $result ? Response::allow() : Response::deny();
    }
}
