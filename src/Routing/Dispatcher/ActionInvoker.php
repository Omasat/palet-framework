<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Dispatcher;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Routing\Dispatcher\ActionInvokerInterface;
use ReflectionFunction;
use ReflectionMethod;
use BadMethodCallException;
use RuntimeException;

class ActionInvoker implements ActionInvokerInterface
{
    protected array $reflectionCache = [];

    public function invoke(mixed $action, RequestInterface $request, array $parameters): mixed
    {
        $reflection = $this->getReflection($action);

        if ($reflection instanceof ReflectionMethod && !$reflection->isPublic()) {
            throw new BadMethodCallException("Action method is not public and cannot be invoked.");
        }

        // Just pass named parameters directly for now (Sprint 18 limitation)
        // In the future (Sprint 19+), this will use Container dependency injection and Reflection type hinting.
        $args = [];
        
        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            
            if (array_key_exists($name, $parameters)) {
                $args[] = $parameters[$name];
            } else {
                if ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    // Fallback to pushing in order if not named properly? Or throw. Let's just use empty for now.
                    // Actually, typically we just pass the values. Let's match by name.
                    $args[] = null; 
                }
            }
        }
        
        // If there are more parameters in the URL than in the method signature, they are ignored.

        return is_callable($action) ? $action(...$args) : throw new BadMethodCallException("Action is not callable.");
    }

    protected function getReflection(mixed $action): ReflectionFunction|ReflectionMethod
    {
        $cacheKey = $this->getCacheKey($action);
        
        if ($cacheKey !== null && isset($this->reflectionCache[$cacheKey])) {
            return $this->reflectionCache[$cacheKey];
        }

        if (is_array($action)) {
            $reflection = new ReflectionMethod($action[0], $action[1]);
        } elseif (is_object($action) && !$action instanceof \Closure) {
            $reflection = new ReflectionMethod($action, '__invoke');
        } else {
            $reflection = new ReflectionFunction($action);
        }
        
        if ($cacheKey !== null) {
            $this->reflectionCache[$cacheKey] = $reflection;
        }
        
        return $reflection;
    }

    protected function getCacheKey(mixed $action): ?string
    {
        if (is_array($action) && is_string($action[0])) {
            return $action[0] . '::' . $action[1];
        }
        
        if (is_string($action)) {
            return $action;
        }
        
        return null; // Closures and objects are hard to safely string-key without spl_object_hash which isn't persistent
    }
}
