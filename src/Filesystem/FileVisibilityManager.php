<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem;

class FileVisibilityManager
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';

    protected int $publicFile = 0644;
    protected int $privateFile = 0600;
    protected int $publicDir = 0755;
    protected int $privateDir = 0700;

    /**
     * Get the permission value for the given visibility.
     */
    public function getVisibility(string $visibility, bool $isDirectory = false): int
    {
        if ($isDirectory) {
            return $visibility === self::VISIBILITY_PUBLIC ? $this->publicDir : $this->privateDir;
        }

        return $visibility === self::VISIBILITY_PUBLIC ? $this->publicFile : $this->privateFile;
    }

    /**
     * Set file or directory permissions based on visibility.
     */
    public function setVisibility(string $path, string $visibility, bool $isDirectory = false): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $permission = $this->getVisibility($visibility, $isDirectory);
        
        return chmod($path, $permission);
    }
}
