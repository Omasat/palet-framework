<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Providers;

use RuntimeException;

class PackageDiscovery
{
    /**
     * @var string
     */
    protected string $vendorPath;

    public function __construct(string $vendorPath)
    {
        $this->vendorPath = $vendorPath;
    }

    /**
     * Composer'ın kurulu paketlerini okuyup 'palet' uyumlu paketleri döndürür.
     *
     * @return array<string, array<string, mixed>>
     */
    public function discover(): array
    {
        $installedJsonPath = $this->vendorPath . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.json';

        if (!file_exists($installedJsonPath)) {
            return [];
        }

        $installed = json_decode(file_get_contents($installedJsonPath), true);
        
        $packages = $installed['packages'] ?? $installed; // composer v1/v2 compatibility

        $discovered = [];

        foreach ($packages as $package) {
            if (isset($package['extra']['palet'])) {
                $discovered[$package['name']] = $package['extra']['palet'];
            }
        }

        return $discovered;
    }
}
