<?php

declare(strict_types=1);

namespace Palet\Framework\Config;

class ConfigLoader
{
    /**
     * Load configuration files from a given directory path.
     * Only .php files returning arrays will be processed.
     *
     * @param string $path
     * @return array<string, mixed>
     */
    public function load(string $path): array
    {
        $items = [];

        if (!is_dir($path)) {
            return $items;
        }

        $files = glob($path . DIRECTORY_SEPARATOR . '*.php');

        if ($files === false) {
            return $items;
        }

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $items[$key] = require $file;
        }

        return $items;
    }
}
