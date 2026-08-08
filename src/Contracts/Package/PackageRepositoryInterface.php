<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Package;

interface PackageRepositoryInterface
{
    /**
     * Find a package by name.
     */
    public function find(string $packageName): ?array;
    
    /**
     * Get available versions for a package.
     */
    public function getVersions(string $packageName): array;
}
