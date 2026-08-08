<?php

declare(strict_types=1);

namespace Palet\Framework\Support\Invocation;

use Palet\Framework\Contracts\Support\Invocation\ParameterResolverInterface;
use ReflectionParameter;
use ReflectionNamedType;
use ReflectionUnionType;
use ReflectionIntersectionType;
use RuntimeException;

class ParameterResolver implements ParameterResolverInterface
{
    protected DependencyResolver $dependencyResolver;

    public function __construct(DependencyResolver $dependencyResolver)
    {
        $this->dependencyResolver = $dependencyResolver;
    }

    public function resolve(ReflectionParameter $parameter, InvocationContext $context): mixed
    {
        $name = $parameter->getName();

        if ($context->hasParameter($name)) {
            return $context->getParameter($name);
        }

        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            $className = $type->getName();
            
            if ($this->dependencyResolver->has($className)) {
                return $this->dependencyResolver->resolve($className);
            }
        }
        
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof ReflectionNamedType && !$unionType->isBuiltin()) {
                    $className = $unionType->getName();
                    if ($this->dependencyResolver->has($className)) {
                        return $this->dependencyResolver->resolve($className);
                    }
                }
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }
        
        if ($parameter->isVariadic()) {
            return []; // Will be spread as nothing if empty
        }

        throw new RuntimeException("Unresolvable dependency resolving [$name] in class {$parameter->getDeclaringClass()?->getName()}");
    }
}
