<?php

declare(strict_types=1);

namespace Palet\Framework\Package;

class PackageManifest
{
    public function parse(string $manifestPath): array
    {
        if (!file_exists($manifestPath)) {
            return [];
        }

        $content = file_get_contents($manifestPath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid package manifest JSON in {$manifestPath}.");
        }

        return $data;
    }

    public function getDependencies(string $manifestPath): array
    {
        $data = $this->parse($manifestPath);
        return $data['dependencies'] ?? [];
    }

    public function getName(string $manifestPath): ?string
    {
        $data = $this->parse($manifestPath);
        return $data['name'] ?? null;
    }
}
