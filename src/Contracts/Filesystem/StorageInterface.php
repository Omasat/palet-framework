<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Filesystem;

interface StorageInterface
{
    /**
     * Get a filesystem instance.
     */
    public function disk(?string $name = null): FilesystemInterface;
}
