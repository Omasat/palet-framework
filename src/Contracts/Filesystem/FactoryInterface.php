<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Filesystem;

interface FactoryInterface
{
    /**
     * Get a filesystem implementation.
     */
    public function disk(?string $name = null): StorageDriverInterface;
}
