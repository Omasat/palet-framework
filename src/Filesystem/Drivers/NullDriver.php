<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem\Drivers;

use Palet\Framework\Contracts\Filesystem\StorageDriverInterface;

class NullDriver implements StorageDriverInterface
{
    public function exists(string $path): bool
    {
        return false;
    }

    public function read(string $path): ?string
    {
        return null;
    }

    public function readStream(string $path)
    {
        return null;
    }

    public function write(string $path, string $contents, array $options = []): bool
    {
        return true;
    }

    public function writeStream(string $path, $resource, array $options = []): bool
    {
        return true;
    }

    public function append(string $path, string $data): bool
    {
        return true;
    }

    public function prepend(string $path, string $data): bool
    {
        return true;
    }

    public function copy(string $from, string $to): bool
    {
        return true;
    }

    public function move(string $from, string $to): bool
    {
        return true;
    }

    public function delete(string|array $paths): bool
    {
        return true;
    }

    public function createDirectory(string $path): bool
    {
        return true;
    }

    public function deleteDirectory(string $directory): bool
    {
        return true;
    }

    public function size(string $path): int
    {
        return 0;
    }

    public function lastModified(string $path): int
    {
        return 0;
    }

    public function mimeType(string $path): string
    {
        return 'application/octet-stream';
    }

    public function visibility(string $path): string
    {
        return 'public';
    }

    public function setVisibility(string $path, string $visibility): bool
    {
        return true;
    }

    public function listFiles(string $directory, bool $recursive = false): array
    {
        return [];
    }

    public function listDirectories(string $directory, bool $recursive = false): array
    {
        return [];
    }

    public function url(string $path): string
    {
        return '';
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = []): string
    {
        return '';
    }
}
