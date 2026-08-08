<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Discovery;

use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Console\Attributes\HiddenCommand;
use Palet\Framework\Contracts\Console\CommandInterface;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CommandScanner
{
    public function scan(array $directories): array
    {
        $commands = [];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            
            foreach ($iterator as $file) {
                if ($file->getExtension() === 'php') {
                    $class = $this->extractClassFromFile($file->getPathname());
                    
                    if ($class) {
                        require_once $file->getPathname();
                        if (class_exists($class)) {
                            $metadata = $this->inspectClass($class);
                            if ($metadata) {
                                $commands[$metadata->name] = $metadata;
                            }
                        }
                    }
                }
            }
        }

        return $commands;
    }

    protected function inspectClass(string $class): ?CommandMetadata
    {
        $reflection = new ReflectionClass($class);

        if ($reflection->isAbstract() || !$reflection->implementsInterface(CommandInterface::class)) {
            return null;
        }

        $name = null;
        $description = '';
        $hidden = false;

        $asCommand = $reflection->getAttributes(AsCommand::class);
        if (!empty($asCommand)) {
            $attribute = $asCommand[0]->newInstance();
            $name = $attribute->name;
            $description = $attribute->description;
        }

        $hiddenCommand = $reflection->getAttributes(HiddenCommand::class);
        if (!empty($hiddenCommand)) {
            $hidden = true;
        }

        // Fallback to class properties if attributes are missing
        if (!$name) {
            $instance = $reflection->newInstanceWithoutConstructor();
            $name = $instance->getName();
            $description = $instance->getDescription();
        }

        if (!$name) {
            return null;
        }

        return new CommandMetadata($name, $class, $description, $hidden);
    }

    protected function extractClassFromFile(string $file): ?string
    {
        $content = file_get_contents($file);
        $namespace = '';
        $class = '';

        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1] . '\\';
        }

        if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $content, $matches)) {
            $class = $matches[1];
        }

        return $class ? $namespace . $class : null;
    }
}
