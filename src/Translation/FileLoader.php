<?php

declare(strict_types=1);

namespace Palet\Framework\Translation;

use Palet\Framework\Contracts\Translation\TranslationLoaderInterface;

class FileLoader implements TranslationLoaderInterface
{
    protected string $path;
    protected array $hints = [];
    protected array $jsonPaths = [];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function load(string $locale, string $group, ?string $namespace = null): array
    {
        if ($group === '*' && $namespace === '*') {
            return $this->loadJsonPaths($locale);
        }

        if ($namespace !== null && isset($this->hints[$namespace])) {
            return $this->loadPath($this->hints[$namespace], $locale, $group);
        }

        return $this->loadPath($this->path, $locale, $group);
    }

    protected function loadPath(string $path, string $locale, string $group): array
    {
        $file = "{$path}/{$locale}/{$group}.php";

        if (file_exists($file)) {
            return require $file;
        }

        return [];
    }

    protected function loadJsonPaths(string $locale): array
    {
        $output = [];

        $paths = array_merge([$this->path], $this->jsonPaths);

        foreach ($paths as $path) {
            $file = "{$path}/{$locale}.json";
            
            if (file_exists($file)) {
                $decoded = json_decode(file_get_contents($file), true);
                
                if (is_array($decoded)) {
                    $output = array_merge($output, $decoded);
                }
            }
        }

        return $output;
    }

    public function addNamespace(string $namespace, string $hint): void
    {
        $this->hints[$namespace] = $hint;
    }

    public function addJsonPath(string $path): void
    {
        $this->jsonPaths[] = $path;
    }
}
