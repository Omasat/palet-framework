<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Container;

use Closure;

interface ContextualBindingBuilderInterface
{
    /**
     * Define the abstract target that depends on the context.
     */
    public function needs(string $abstract): self;

    /**
     * Define the implementation for the contextual binding.
     */
    public function give(mixed $implementation): void;
}
