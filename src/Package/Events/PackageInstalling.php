<?php

declare(strict_types=1);

namespace Palet\Framework\Package\Events;

class PackageInstalling
{
    public function __construct(
        public readonly string $packageName,
        public readonly string $version
    ) {}
}
