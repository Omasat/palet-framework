<?php

declare(strict_types=1);

namespace Palet\Framework\Asset;

class AssetManifest
{
    protected string $manifestPath;
    protected ?array $manifest = null;

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    public function get(string $key): ?array
    {
        $this->load();
        return $this->manifest[$key] ?? null;
    }

    protected function load(): void
    {
        if ($this->manifest !== null) {
            return;
        }

        if (!file_exists($this->manifestPath)) {
            $this->manifest = [];
            return;
        }

        $content = file_get_contents($this->manifestPath);
        $this->manifest = json_decode($content, true) ?? [];
    }
}
