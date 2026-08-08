<?php

declare(strict_types=1);

namespace Palet\Framework\Container;

use Palet\Framework\Contracts\Container\AliasRepositoryInterface;

class AliasRepository implements AliasRepositoryInterface
{
    /** @var array<string, string> */
    private array $aliases = [];

    public function alias(string $abstract, string $alias): void
    {
        if ($alias === $abstract) {
            throw new \LogicException("Cannot alias '{$abstract}' to itself.");
        }
        $this->aliases[$alias] = $abstract;
    }

    public function getAlias(string $alias): string
    {
        if (!isset($this->aliases[$alias])) {
            return $alias;
        }

        if ($this->aliases[$alias] === $alias) {
            throw new \LogicException("Circular alias detected for '{$alias}'.");
        }

        return $this->getAlias($this->aliases[$alias]);
    }

    public function isAlias(string $name): bool
    {
        return isset($this->aliases[$name]);
    }
}
