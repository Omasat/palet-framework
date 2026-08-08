<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Package;

interface PackagePublisherInterface
{
    /**
     * Publish assets, config, and views from a package to the application.
     */
    public function publish(string $packageName, array $tags = [], bool $force = false): void;
}
