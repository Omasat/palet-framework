<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold;

use Palet\Framework\Contracts\Scaffold\ProjectValidatorInterface;
use InvalidArgumentException;
use RuntimeException;

class ProjectValidator implements ProjectValidatorInterface
{
    public function validate(string $targetPath): void
    {
        if (empty(trim($targetPath))) {
            throw new InvalidArgumentException("Project path cannot be empty.");
        }

        // Prevent path traversal above root
        $normalized = str_replace('\\', '/', $targetPath);
        if (strpos($normalized, '../') !== false) {
            throw new InvalidArgumentException("Path traversal detected: [{$targetPath}]");
        }

        if (file_exists($targetPath)) {
            $files = array_diff(scandir($targetPath), ['.', '..']);
            if (!empty($files)) {
                throw new RuntimeException("Target directory [{$targetPath}] is not empty.");
            }
        }
    }
}
