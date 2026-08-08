<?php

declare(strict_types=1);

namespace Palet\Framework\Package\Events;

class PackageInstalled
{
    public function __construct(
        public readonly string $packageName,
        public readonly string $version,
        public readonly string $installPath
    ) {}
}
