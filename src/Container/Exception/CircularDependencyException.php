<?php

declare(strict_types=1);

namespace Palet\Framework\Container\Exception;

class CircularDependencyException extends ContainerException
{
    public static function create(string $abstract, array $buildStack): self
    {
        $stack = implode(' -> ', $buildStack) . ' -> ' . $abstract;
        return new self("Circular dependency detected while resolving '{$abstract}'. Stack: {$stack}");
    }
}
