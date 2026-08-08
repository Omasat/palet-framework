<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Package;

interface DependencyResolverInterface
{
    /**
     * Resolve dependencies for a given package and version constraint.
     * Returns an array of resolved packages and their exact versions.
     */
    public function resolve(string $packageName, string $versionConstraint): array;
}
