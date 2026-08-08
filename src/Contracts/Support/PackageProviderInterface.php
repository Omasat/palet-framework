<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Support;

/**
 * Amaç: Üçüncü taraf (Third-Party) paketlerin sağlayıcıları için eklenti arayüzü.
 * Sorumluluk: Paketin adını (namespace/name) ve paket dizinlerini sağlamak.
 */
interface PackageProviderInterface extends ServiceProviderInterface
{
    /**
     * Get the package name (e.g., 'vendor/package').
     */
    public function packageName(): string;

    /**
     * Get the base path for the package.
     */
    public function packagePath(): string;
}
