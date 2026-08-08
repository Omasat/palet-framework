<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Package;

interface PackageManagerInterface
{
    /**
     * Install a new package.
     */
    public function install(string $packageName, ?string $versionConstraint = null): void;

    /**
     * Remove an installed package.
     */
    public function remove(string $packageName): void;

    /**
     * Update an installed package.
     */
    public function update(string $packageName): void;
}
