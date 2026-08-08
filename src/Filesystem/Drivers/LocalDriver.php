<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem\Drivers;

use Palet\Framework\Contracts\Filesystem\StorageDriverInterface;
use Palet\Framework\Filesystem\PathNormalizer;
use RuntimeException;
use InvalidArgumentException;

class LocalDriver implements StorageDriverInterface
{
    protected string $root;

    public function __construct(string $root)
    {
        $this->root = rtrim($root, '/\\') . DIRECTORY_SEPARATOR;
    }

    protected function getAbsolutePath(string $path): string
    {
        $normalized = PathNormalizer::normalize($path);
        return $this->root . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getAbsolutePath($path));
    }

    public function read(string $path): ?string
    {
        if (!$this->exists($path)) {
            return null;
        }

        $content = file_get_contents($this->getAbsolutePath($path));
        return $content === false ? null : $content;
    }

    public function readStream(string $path)
    {
        if (!$this->exists($path)) {
            return null;
        }

        $stream = fopen($this->getAbsolutePath($path), 'rb');
        
        if ($stream === false) {
            throw new RuntimeException("Could not open stream for reading: [{$path}]");
        }
        
        return $stream;
    }

    public function write(string $path, string $contents, array $options = []): bool
    {
        $absolutePath = $this->getAbsolutePath($path);
        $this->ensureDirectoryExists(dirname($absolutePath));

        return file_put_contents($absolutePath, $contents) !== false;
    }

    public function writeStream(string $path, $resource, array $options = []): bool
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Provided data must be a valid resource stream.');
        }

        $absolutePath = $this->getAbsolutePath($path);
        $this->ensureDirectoryExists(dirname($absolutePath));

        $targetStream = fopen($absolutePath, 'wb');
        if ($targetStream === false) {
            return false;
        }

        $bytesCopied = stream_copy_to_stream($resource, $targetStream);
        fclose($targetStream);

        return $bytesCopied !== false;
    }

    public function append(string $path, string $data): bool
    {
        $absolutePath = $this->getAbsolutePath($path);
        $this->ensureDirectoryExists(dirname($absolutePath));

        return file_put_contents($absolutePath, $data, FILE_APPEND) !== false;
    }

    public function prepend(string $path, string $data): bool
    {
        if ($this->exists($path)) {
            return $this->write($path, $data . $this->read($path));
        }

        return $this->write($path, $data);
    }

    public function copy(string $from, string $to): bool
    {
        if (!$this->exists($from)) {
            return false;
        }

        $absoluteTo = $this->getAbsolutePath($to);
        $this->ensureDirectoryExists(dirname($absoluteTo));

        return copy($this->getAbsolutePath($from), $absoluteTo);
    }

    public function move(string $from, string $to): bool
    {
        if (!$this->exists($from)) {
            return false;
        }

        $absoluteTo = $this->getAbsolutePath($to);
        $this->ensureDirectoryExists(dirname($absoluteTo));

        return rename($this->getAbsolutePath($from), $absoluteTo);
    }

    public function delete(string|array $paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();
        $success = true;

        foreach ($paths as $path) {
            if ($this->exists($path)) {
                if (!unlink($this->getAbsolutePath($path))) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    public function createDirectory(string $path): bool
    {
        return $this->ensureDirectoryExists($this->getAbsolutePath($path));
    }

    public function deleteDirectory(string $directory): bool
    {
        $absolutePath = $this->getAbsolutePath($directory);
        
        if (!is_dir($absolutePath)) {
            return false;
        }

        $files = array_diff(scandir($absolutePath), ['.', '..']);
        
        foreach ($files as $file) {
            $filePath = $absolutePath . DIRECTORY_SEPARATOR . $file;
            is_dir($filePath) ? $this->deleteDirectory($directory . '/' . $file) : unlink($filePath);
        }

        return rmdir($absolutePath);
    }

    public function size(string $path): int
    {
        return filesize($this->getAbsolutePath($path)) ?: 0;
    }

    public function lastModified(string $path): int
    {
        return filemtime($this->getAbsolutePath($path)) ?: 0;
    }

    public function mimeType(string $path): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $this->getAbsolutePath($path));
        finfo_close($finfo);

        return $mime ?: 'application/octet-stream';
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
        $absolutePath = $this->getAbsolutePath($directory);
        if (!is_dir($absolutePath)) return [];

        $files = [];
        $iterator = $recursive 
            ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($absolutePath, \FilesystemIterator::SKIP_DOTS))
            : new \FilesystemIterator($absolutePath, \FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $files[] = str_replace($this->root, '', $item->getPathname());
            }
        }

        return $files;
    }

    public function listDirectories(string $directory, bool $recursive = false): array
    {
        $absolutePath = $this->getAbsolutePath($directory);
        if (!is_dir($absolutePath)) return [];

        $directories = [];
        $iterator = $recursive 
            ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($absolutePath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST)
            : new \FilesystemIterator($absolutePath, \FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $directories[] = str_replace($this->root, '', $item->getPathname());
            }
        }

        return $directories;
    }

    public function url(string $path): string
    {
        return '/storage/' . PathNormalizer::normalize($path);
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = []): string
    {
        throw new RuntimeException("This driver does not support creating temporary URLs.");
    }

    protected function ensureDirectoryExists(string $path): bool
    {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }
}
