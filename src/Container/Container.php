<?php

declare(strict_types=1);

namespace Palet\Framework\Container;

use Closure;
use Palet\Framework\Contracts\Container\ContainerInterface;
use Palet\Framework\Contracts\Container\ContextualBindingBuilderInterface;
use Palet\Framework\Container\Exception\BindingResolutionException;
use Palet\Framework\Container\Exception\CircularDependencyException;
use Palet\Framework\Container\Exception\NotFoundException;
use ReflectionParameter;

class Container implements ContainerInterface
{
    protected array $bindings = [];
    protected array $instances = [];

    /**
     * The current globally available container (if any).
     */
    protected static ?ContainerInterface $instance = null;

    /**
     * Contextual bindings resolver builder.
     */
    public array $contextual = [];
    protected array $buildStack = [];

    protected AliasRepository $aliases;
    protected ReflectionResolver $resolver;

    public function __construct()
    {
        $this->aliases = new AliasRepository();
        $this->resolver = new ReflectionResolver();
    }

    /**
     * Get the globally available instance of the container.
     */
    public static function getInstance(): ?ContainerInterface
    {
        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     */
    public static function setInstance(?ContainerInterface $container = null): ?ContainerInterface
    {
        return static::$instance = $container;
    }

    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = new Binding($concrete, $shared);
    }

    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function instance(string $abstract, mixed $instance): mixed
    {
        $this->instances[$abstract] = $instance;
        return $instance;
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->aliases->alias($abstract, $alias);
    }

    public function when(array|string $concrete): ContextualBindingBuilderInterface
    {
        return new ContextualBindingBuilder($this, $concrete);
    }

    public function addContextualBinding(string $concrete, string $abstract, mixed $implementation): void
    {
        $this->contextual[$concrete][$abstract] = $implementation;
    }

    protected function getContextualConcrete(string $abstract): mixed
    {
        if (empty($this->buildStack)) {
            return null;
        }

        $concrete = array_key_last($this->buildStack);

        if (isset($this->contextual[$concrete][$abstract])) {
            return $this->contextual[$concrete][$abstract];
        }

        return null;
    }

    public function isShared(string $abstract): bool
    {
        return isset($this->instances[$abstract]) ||
            (isset($this->bindings[$abstract]) && $this->bindings[$abstract]->shared === true);
    }

    public function isAlias(string $name): bool
    {
        return $this->aliases->isAlias($name);
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]) || $this->isAlias($id);
    }

    public function get(string $id): mixed
    {
        try {
            return $this->resolve($id);
        } catch (BindingResolutionException $e) {
            if ($this->has($id)) {
                throw $e;
            }
            throw new NotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        return $this->resolve($abstract, $parameters);
    }

    protected function resolve(string $abstract, array $parameters = []): mixed
    {
        $abstract = $this->aliases->getAlias($abstract);

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getContextualConcrete($abstract) ?? $this->getConcrete($abstract);

        if (isset($this->buildStack[$abstract])) {
            throw CircularDependencyException::create($abstract, array_keys($this->buildStack));
        }

        $this->buildStack[$abstract] = true;

        try {
            if ($this->isBuildable($concrete, $abstract)) {
                $object = $this->build($concrete, $parameters);
            } else {
                $object = $this->make($concrete, $parameters);
            }
        } finally {
            unset($this->buildStack[$abstract]);
        }

        if ($this->isShared($abstract) && !isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    protected function getConcrete(string $abstract): mixed
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]->concrete;
        }

        return $abstract;
    }

    protected function isBuildable(mixed $concrete, string $abstract): bool
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    protected function build(mixed $concrete, array $parameters): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        if (!is_string($concrete)) {
            return $concrete; // Already resolved value (e.g., from contextual give(primitive))
        }

        $dependencies = $this->resolver->getDependencies($concrete);
        $instances = $this->resolveDependencies($dependencies, $parameters);

        return new $concrete(...$instances);
    }

    /**
     * @param ReflectionParameter[] $dependencies
     * @return array
     */
    protected function resolveDependencies(array $dependencies, array $parameters): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $name = $dependency->getName();

            if (array_key_exists($name, $parameters)) {
                $results[] = $parameters[$name];
                continue;
            }

            $className = $this->resolver->getParameterClassName($dependency);

            if ($className !== null) {
                try {
                    $results[] = $this->make($className);
                } catch (BindingResolutionException $e) {
                    if ($dependency->isDefaultValueAvailable()) {
                        $results[] = $dependency->getDefaultValue();
                    } elseif ($dependency->allowsNull()) {
                        $results[] = null;
                    } else {
                        throw $e;
                    }
                }
                continue;
            }

            $concrete = array_key_last($this->buildStack);
            if (isset($this->contextual[$concrete][\sprintf('$%s', $name)])) {
                $contextualValue = $this->contextual[$concrete][\sprintf('$%s', $name)];
                $results[] = $contextualValue instanceof Closure ? $contextualValue($this) : $contextualValue;
                continue;
            }

            if ($dependency->isDefaultValueAvailable()) {
                $results[] = $dependency->getDefaultValue();
            } elseif ($dependency->allowsNull()) {
                $results[] = null;
            } else {
                throw new BindingResolutionException("Unresolvable primitive dependency resolving [$name].");
            }
        }

        return $results;
    }
}
