<?php

declare(strict_types=1);

namespace Palet\Framework\Container;

use Closure;
use Palet\Framework\Contracts\Container\ContextualBindingBuilderInterface;

class ContextualBindingBuilder implements ContextualBindingBuilderInterface
{
    protected string $needs = '';

    public function __construct(
        protected Container $container,
        protected array|string $concrete
    ) {
    }

    public function needs(string $abstract): self
    {
        $this->needs = $abstract;

        return $this;
    }

    public function give(mixed $implementation): void
    {
        foreach ((array) $this->concrete as $concrete) {
            $this->container->addContextualBinding($concrete, $this->needs, $implementation);
        }
    }
}
