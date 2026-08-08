<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Discovery;

class CommandManifest
{
    public function __construct(
        protected string $manifestPath
    ) {
    }

    public function build(CommandScanner $scanner, array $directories): void
    {
        $commands = $scanner->scan($directories);
        $data = array_map(fn($metadata) => $metadata->toArray(), $commands);

        $content = "<?php\n\nreturn " . var_export($data, true) . ";\n";
        
        $dir = dirname($this->manifestPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->manifestPath, $content);
    }

    public function load(): array
    {
        if (!file_exists($this->manifestPath)) {
            return [];
        }

        $data = require $this->manifestPath;
        
        $commands = [];
        foreach ($data as $item) {
            $commands[$item['name']] = CommandMetadata::fromArray($item);
        }

        return $commands;
    }
}
