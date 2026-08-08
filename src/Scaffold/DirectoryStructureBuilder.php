<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold;

use Palet\Framework\Contracts\Scaffold\TemplateInterface;

class DirectoryStructureBuilder
{
    public function build(string $targetPath, TemplateInterface $template): void
    {
        $directories = $template->getDirectoryStructure();

        foreach ($directories as $dir) {
            $path = $targetPath . DIRECTORY_SEPARATOR . ltrim($dir, '/\\');
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            
            // Put a .gitkeep to ensure empty directories are tracked
            file_put_contents($path . DIRECTORY_SEPARATOR . '.gitkeep', '');
        }

        // Write template files
        foreach ($template->getFiles() as $file => $content) {
            $filePath = $targetPath . DIRECTORY_SEPARATOR . ltrim($file, '/\\');
            
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($filePath, $content);
        }
    }
}
