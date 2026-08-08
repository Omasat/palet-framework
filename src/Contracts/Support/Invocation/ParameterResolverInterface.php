<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Support\Invocation;

use ReflectionParameter;
use Palet\Framework\Support\Invocation\InvocationContext;

interface ParameterResolverInterface
{
    /**
     * Resolve the value for a given reflection parameter.
     */
    public function resolve(ReflectionParameter $parameter, InvocationContext $context): mixed;
}
