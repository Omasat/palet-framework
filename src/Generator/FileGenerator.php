<?php

declare(strict_types=1);

namespace Palet\Framework\Generator;

use RuntimeException;

class FileGenerator
{
    public function generate(string $destination, string $content, bool $force = false, bool $dryRun = false): bool
    {
        // Normalize path to prevent traversal attacks
        $normalized = str_replace('\\', '/', $destination);
        if (str_contains($normalized, '../')) {
            throw new RuntimeException("Path traversal detected in destination: {$destination}");
        }

        if (file_exists($destination) && !$force) {
            throw new RuntimeException("File already exists at {$destination}. Use force to overwrite.");
        }

        if ($dryRun) {
            // In dry run, we just return true indicating it would succeed
            return true;
        }

        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($destination, $content) !== false;
    }
}
