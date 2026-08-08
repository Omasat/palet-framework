<?php

declare(strict_types=1);

namespace Palet\Framework\Support\Invocation;

use Palet\Framework\Contracts\Support\Invocation\MethodInvokerInterface;
use BadMethodCallException;

class MethodInvoker implements MethodInvokerInterface
{
    protected ReflectionMetadataCache $cache;
    protected ArgumentMapper $mapper;

    public function __construct(ReflectionMetadataCache $cache, ArgumentMapper $mapper)
    {
        $this->cache = $cache;
        $this->mapper = $mapper;
    }

    public function invoke(mixed $action, ?InvocationContext $context = null): mixed
    {
        $context = $context ?? new InvocationContext();

        if (!$this->cache->isPublic($action)) {
            throw new BadMethodCallException("Action is not public and cannot be invoked.");
        }

        $parameters = $this->cache->getParameters($action);
        $args = $this->mapper->map($parameters, $context);

        return is_callable($action) ? $action(...$args) : throw new BadMethodCallException("Action is not callable.");
    }
}
