<?php

declare(strict_types=1);

namespace Palet\Framework\Session\Drivers;

use SessionHandlerInterface;

class FileSessionDriver implements SessionHandlerInterface
{
    protected string $path;
    protected int $minutes;

    public function __construct(string $path, int $minutes)
    {
        $this->path = $path;
        $this->minutes = $minutes;
        
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0755, true);
        }
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $file = $this->path . '/' . $id;
        
        if (file_exists($file)) {
            if (filemtime($file) >= time() - ($this->minutes * 60)) {
                return file_get_contents($file);
            }
        }
        
        return '';
    }

    public function write(string $id, string $data): bool
    {
        return file_put_contents($this->path . '/' . $id, $data, LOCK_EX) !== false;
    }

    public function destroy(string $id): bool
    {
        $file = $this->path . '/' . $id;
        if (file_exists($file)) {
            @unlink($file);
        }
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $deleted = 0;
        $time = time();
        $files = glob($this->path . '/*');
        
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $time - $max_lifetime) {
                    @unlink($file);
                    $deleted++;
                }
            }
        }
        return $deleted;
    }
}
