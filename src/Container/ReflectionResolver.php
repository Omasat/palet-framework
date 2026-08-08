<?php

declare(strict_types=1);

namespace Palet\Framework\Container;

use Palet\Framework\Container\Exception\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

class ReflectionResolver
{
    /** @var array<string, array<ReflectionParameter>> */
    protected static array $dependencyCache = [];
    
    /** @var array<string, ?string> */
    protected static array $typeCache = [];
    /**
     * Determine dependencies for the given class.
     *
     * @param string $concrete
     * @return ReflectionParameter[]
     * @throws BindingResolutionException
     */
    public function getDependencies(string $concrete): array
    {
        if (isset(self::$dependencyCache[$concrete])) {
            return self::$dependencyCache[$concrete];
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new BindingResolutionException("Target class [{$concrete}] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new BindingResolutionException("Target [{$concrete}] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return self::$dependencyCache[$concrete] = [];
        }

        return self::$dependencyCache[$concrete] = $constructor->getParameters();
    }

    /**
     * Get the type name from a parameter.
     */
    public function getParameterClassName(ReflectionParameter $parameter): ?string
    {
        // Parameter might not have a clean string key, but we can build one:
        // Function/Method name + Parameter name
        $func = $parameter->getDeclaringFunction();
        $key = ($func instanceof \ReflectionMethod ? $func->class . '::' : '') . $func->name . '$' . $parameter->name;
        
        if (array_key_exists($key, self::$typeCache)) {
            return self::$typeCache[$key];
        }
        
        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return self::$typeCache[$key] = null;
        }

        return self::$typeCache[$key] = $type->getName();
    }
}
