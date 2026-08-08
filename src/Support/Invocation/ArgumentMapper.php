<?php

declare(strict_types=1);

namespace Palet\Framework\Support\Invocation;

use Palet\Framework\Contracts\Support\Invocation\ParameterResolverInterface;

class ArgumentMapper
{
    protected ParameterResolverInterface $parameterResolver;

    public function __construct(ParameterResolverInterface $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    public function map(array $reflectionParameters, InvocationContext $context): array
    {
        $args = [];

        foreach ($reflectionParameters as $parameter) {
            /** @var \ReflectionParameter $parameter */
            $value = $this->parameterResolver->resolve($parameter, $context);
            
            if ($parameter->isVariadic()) {
                if (is_array($value)) {
                    $args = array_merge($args, array_values($value));
                } else {
                    $args[] = $value;
                }
            } else {
                $args[] = $value;
            }
        }

        return $args;
    }
}
