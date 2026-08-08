<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Support\Invocation;

use Palet\Framework\Support\Invocation\InvocationContext;

interface MethodInvokerInterface
{
    /**
     * Invoke the given callable with resolved dependencies.
     */
    public function invoke(mixed $action, ?InvocationContext $context = null): mixed;
}
