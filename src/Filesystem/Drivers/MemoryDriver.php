<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem\Drivers;

use Palet\Framework\Contracts\Filesystem\StorageDriverInterface;
use Palet\Framework\Filesystem\PathNormalizer;
use RuntimeException;
use InvalidArgumentException;

class MemoryDriver implements StorageDriverInterface
{
    protected array $files = [];

    protected function getNormalizedPath(string $path): string
    {
        return PathNormalizer::normalize($path);
    }

    public function exists(string $path): bool
    {
        return isset($this->files[$this->getNormalizedPath($path)]);
    }

    public function read(string $path): ?string
    {
        $normalized = $this->getNormalizedPath($path);
        
        if (!isset($this->files[$normalized])) {
            return null;
        }

        return $this->files[$normalized]['content'];
    }

    public function readStream(string $path)
    {
        $content = $this->read($path);
        if ($content === null) {
            return null;
        }

        $stream = fopen('php://temp', 'rb+');
        fwrite($stream, $content);
        rewind($stream);
        
        return $stream;
    }

    public function write(string $path, string $contents, array $options = []): bool
    {
        $normalized = $this->getNormalizedPath($path);
        
        $this->files[$normalized] = [
            'content' => $contents,
            'size' => strlen($contents),
            'last_modified' => time()
        ];
        
        return true;
    }

    public function writeStream(string $path, $resource, array $options = []): bool
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Provided data must be a valid resource stream.');
        }

        $content = stream_get_contents($resource);
        if ($content === false) {
            return false;
        }

        return $this->write($path, $content, $options);
    }

    public function append(string $path, string $data): bool
    {
        $current = $this->read($path) ?? '';
        return $this->write($path, $current . $data);
    }

    public function prepend(string $path, string $data): bool
    {
        $current = $this->read($path) ?? '';
        return $this->write($path, $data . $current);
    }

    public function copy(string $from, string $to): bool
    {
        if (!$this->exists($from)) {
            return false;
        }

        return $this->write($to, $this->read($from));
    }

    public function move(string $from, string $to): bool
    {
        if (!$this->exists($from)) {
            return false;
        }

        $this->write($to, $this->read($from));
        $this->delete($from);
        
        return true;
    }

    public function delete(string|array $paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        foreach ($paths as $path) {
            $normalized = $this->getNormalizedPath($path);
            unset($this->files[$normalized]);
        }

        return true;
    }

    public function createDirectory(string $path): bool
    {
        // Memory driver simulates files, directories are implicitly created via file paths.
        return true;
    }

    public function deleteDirectory(string $directory): bool
    {
        $prefix = $this->getNormalizedPath($directory) . '/';

        foreach (array_keys($this->files) as $path) {
            if (strpos($path, $prefix) === 0) {
                unset($this->files[$path]);
            }
        }

        return true;
    }

    public function size(string $path): int
    {
        $normalized = $this->getNormalizedPath($path);
        
        if (!isset($this->files[$normalized])) {
            return 0;
        }
        
        return $this->files[$normalized]['size'];
    }

    public function lastModified(string $path): int
    {
        $normalized = $this->getNormalizedPath($path);
        
        if (!isset($this->files[$normalized])) {
            return 0;
        }
        
        return $this->files[$normalized]['last_modified'];
    }

    public function mimeType(string $path): string
    {
        return 'text/plain'; // Mock
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
        $prefix = ltrim($this->getNormalizedPath($directory) . '/', '/');
        if ($prefix === '/') {
            $prefix = '';
        }
        
        $files = [];

        foreach (array_keys($this->files) as $path) {
            if ($prefix === '' || strpos($path, $prefix) === 0) {
                $subPath = substr($path, strlen($prefix));
                if ($recursive || strpos($subPath, '/') === false) {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }

    public function listDirectories(string $directory, bool $recursive = false): array
    {
        return [];
    }

    public function url(string $path): string
    {
        return '/memory/' . $this->getNormalizedPath($path);
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = []): string
    {
        return $this->url($path) . '?temp=1';
    }
}
