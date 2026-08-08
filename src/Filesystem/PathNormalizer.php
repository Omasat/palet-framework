<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem;

use InvalidArgumentException;

class PathNormalizer
{
    /**
     * Normalize a path and prevent directory traversal/escape.
     *
     * @param string $path
     * @return string
     * @throws InvalidArgumentException
     */
    public static function normalize(string $path): string
    {
        // Remove trailing slashes and normalize slashes
        $path = str_replace('\\', '/', $path);
        
        $parts = explode('/', $path);
        $safeParts = [];

        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                if (empty($safeParts)) {
                    throw new InvalidArgumentException("Path traversal detected: [{$path}] escapes root directory.");
                }
                array_pop($safeParts);
            } else {
                $safeParts[] = $part;
            }
        }

        return implode('/', $safeParts);
    }
}
