<?php

declare(strict_types=1);

namespace Palet\Framework\View;

use Palet\Framework\Contracts\View\ViewFinderInterface;
use InvalidArgumentException;

class FileViewFinder implements ViewFinderInterface
{
    protected array $paths;
    protected array $extensions;

    public function __construct(array $paths, array $extensions = ['palet.php', 'php'])
    {
        $this->paths = $paths;
        $this->extensions = $extensions;
    }

    public function find(string $view): string
    {
        $viewPath = str_replace('.', '/', $view);

        foreach ($this->paths as $path) {
            foreach ($this->extensions as $extension) {
                $file = $path . '/' . $viewPath . '.' . $extension;
                if (file_exists($file)) {
                    return $file;
                }
            }
        }

        throw new InvalidArgumentException("View [{$view}] not found.");
    }

    public function addLocation(string $location): void
    {
        $this->paths[] = $location;
    }

    public function addNamespace(string $namespace, string|array $hints): void
    {
        // For simplicity, we just ignore namespaces in this basic version,
        // or one could store them in a $namespaces array and resolve `namespace::view`.
    }
}
