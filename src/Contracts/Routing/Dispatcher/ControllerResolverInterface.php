<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing\Dispatcher;

interface ControllerResolverInterface
{
    /**
     * Resolve a controller instance from the given class name.
     */
    public function resolve(string $class): object;
}
