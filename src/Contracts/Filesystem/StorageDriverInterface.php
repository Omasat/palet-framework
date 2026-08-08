<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Filesystem;

/**
 * StorageDriverInterface represents a driver that provides filesystem operations.
 */
interface StorageDriverInterface extends FilesystemInterface
{
    /**
     * Get the URL for the file at the given path.
     */
    public function url(string $path): string;

    /**
     * Get a temporary URL for the file at the given path.
     */
    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = []): string;
}
