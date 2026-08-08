<?php

declare(strict_types=1);

namespace Palet\Framework\Package;

use Palet\Framework\Contracts\Package\PackagePublisherInterface;

class PackagePublisher implements PackagePublisherInterface
{
    protected string $projectRoot;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    public function publish(string $packagePath, array $tags = [], bool $force = false): void
    {
        $manifest = new PackageManifest();
        $manifestPath = $packagePath . '/palet.json';
        
        $data = $manifest->parse($manifestPath);
        $publishable = $data['extra']['palet']['publish'] ?? [];

        foreach ($publishable as $tag => $paths) {
            if (!empty($tags) && !in_array($tag, $tags)) {
                continue;
            }

            foreach ($paths as $source => $destination) {
                $absoluteSource = $packagePath . DIRECTORY_SEPARATOR . $source;
                $absoluteDestination = $this->projectRoot . DIRECTORY_SEPARATOR . $destination;

                $this->copy($absoluteSource, $absoluteDestination, $force);
            }
        }
    }

    protected function copy(string $source, string $destination, bool $force): void
    {
        if (!file_exists($source)) {
            return;
        }

        if (is_dir($source)) {
            $this->copyDirectory($source, $destination, $force);
        } else {
            $this->copyFile($source, $destination, $force);
        }
    }

    protected function copyFile(string $source, string $destination, bool $force): void
    {
        if (file_exists($destination) && !$force) {
            return;
        }

        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        copy($source, $destination);
    }

    protected function copyDirectory(string $source, string $destination, bool $force): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $targetPath = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                $this->copyFile($item->getPathname(), $targetPath, $force);
            }
        }
    }
}
