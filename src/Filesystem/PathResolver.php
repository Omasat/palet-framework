<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem;

use InvalidArgumentException;

class PathResolver
{
    /**
     * Güvenli bir absolute path oluşturur ve Directory Traversal saldırılarını önler.
     *
     * @param string $root Kök dizin (örn: storage/app)
     * @param string $path Gelen dosya yolu (örn: ../../etc/passwd)
     * @return string Temizlenmiş ve güvenli yol
     * @throws InvalidArgumentException
     */
    public function resolve(string $root, string $path): string
    {
        $root = rtrim($root, '\\/');
        $path = ltrim($path, '\\/');

        // Slashes normalize
        $path = str_replace('\\', '/', $path);

        // Prevent traversal
        if (str_contains($path, '../') || str_contains($path, '..\\')) {
            throw new InvalidArgumentException("Directory traversal detected in path: {$path}");
        }

        return $root . DIRECTORY_SEPARATOR . $path;
    }
}
