<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Module;

interface ModuleManifestInterface
{
    public function getName(): string;
    public function getVersion(): string;
    public function isActive(): bool;
    public function getProviders(): array;
}
