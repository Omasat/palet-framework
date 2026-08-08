<?php

declare(strict_types=1);

namespace Palet\Framework\Package;

class PackageValidator
{
    public function validateName(string $name): bool
    {
        // Must follow vendor/package format
        return (bool) preg_match('/^[a-z0-9_.-]+\/[a-z0-9_.-]+$/i', $name);
    }
    
    public function validateExtractPath(string $path): void
    {
        // Simple path traversal check
        $normalized = str_replace('\\', '/', $path);
        if (str_contains($normalized, '../')) {
            throw new \RuntimeException("Path traversal detected in package extraction path: {$path}");
        }
    }
}
