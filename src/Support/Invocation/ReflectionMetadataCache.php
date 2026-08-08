<?php

declare(strict_types=1);

namespace Palet\Framework\Support\Invocation;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use BadMethodCallException;

class ReflectionMetadataCache
{
    protected static array $cache = [];

    public function getParameters(mixed $action): array
    {
        $key = $this->getCacheKey($action);

        if (!isset(self::$cache[$key])) {
            $reflection = $this->getReflection($action);
            self::$cache[$key] = $reflection->getParameters();
        }

        return self::$cache[$key];
    }
    
    public function isPublic(mixed $action): bool
    {
        $reflection = $this->getReflection($action);
        
        if ($reflection instanceof ReflectionMethod) {
            return $reflection->isPublic();
        }
        
        return true;
    }

    protected function getReflection(mixed $action): ReflectionFunction|ReflectionMethod
    {
        if (is_array($action)) {
            return new ReflectionMethod($action[0], $action[1]);
        }

        if (is_object($action) && !$action instanceof \Closure) {
            return new ReflectionMethod($action, '__invoke');
        }
        
        if (!is_callable($action)) {
            throw new BadMethodCallException("Action is not callable.");
        }

        return new ReflectionFunction($action);
    }

    protected function getCacheKey(mixed $action): string
    {
        if (is_array($action)) {
            $class = is_object($action[0]) ? get_class($action[0]) : $action[0];
            return $class . '::' . $action[1];
        }

        if (is_string($action)) {
            return $action;
        }

        if (is_object($action) && !$action instanceof \Closure) {
            return get_class($action) . '::__invoke';
        }

        return spl_object_hash((object)$action); // Closures are objects
    }
    
    public static function clear(): void
    {
        self::$cache = [];
    }
}
