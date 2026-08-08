<?php

declare(strict_types=1);

namespace Palet\Framework\Package;

class PackageDiscovery
{
    public function discover(string $packagePath): array
    {
        $manifest = new PackageManifest();
        $manifestPath = $packagePath . '/palet.json';
        
        $data = $manifest->parse($manifestPath);
        
        return [
            'providers' => $data['extra']['palet']['providers'] ?? [],
            'commands' => $data['extra']['palet']['commands'] ?? [],
            'aliases' => $data['extra']['palet']['aliases'] ?? [],
        ];
    }
}
