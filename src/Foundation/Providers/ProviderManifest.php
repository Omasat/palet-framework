<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Providers;

class ProviderManifest
{
    /**
     * @var string
     */
    protected string $manifestPath;

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * Check if a valid manifest file exists.
     */
    public function exists(): bool
    {
        return file_exists($this->manifestPath);
    }

    /**
     * Load the manifest array.
     *
     * @return array|null
     */
    public function load(): ?array
    {
        if ($this->exists()) {
            $manifest = require $this->manifestPath;

            if (is_array($manifest)) {
                return $manifest;
            }
        }

        return null;
    }

    /**
     * Write the given manifest array to disk.
     */
    public function write(array $manifest): void
    {
        $content = '<?php return ' . var_export($manifest, true) . ';' . PHP_EOL;
        
        file_put_contents($this->manifestPath, $content, LOCK_EX);
    }
}
