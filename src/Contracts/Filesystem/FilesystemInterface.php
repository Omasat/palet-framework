<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Filesystem;

interface FilesystemInterface
{
    public function exists(string $path): bool;
    
    public function read(string $path): ?string;
    
    /**
     * @return resource|null
     */
    public function readStream(string $path);
    
    public function write(string $path, string $contents, array $options = []): bool;
    
    /**
     * @param resource $resource
     */
    public function writeStream(string $path, $resource, array $options = []): bool;
    
    public function append(string $path, string $data): bool;
    
    public function prepend(string $path, string $data): bool;
    
    public function copy(string $from, string $to): bool;
    
    public function move(string $from, string $to): bool;
    
    public function delete(string|array $paths): bool;
    
    public function createDirectory(string $path): bool;
    
    public function deleteDirectory(string $directory): bool;
    
    public function size(string $path): int;
    
    public function lastModified(string $path): int;
    
    public function mimeType(string $path): string;
    
    public function visibility(string $path): string;
    
    public function setVisibility(string $path, string $visibility): bool;
    
    public function listFiles(string $directory, bool $recursive = false): array;
    
    public function listDirectories(string $directory, bool $recursive = false): array;
}
