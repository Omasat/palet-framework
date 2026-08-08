<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Scaffold;

interface TemplateInterface
{
    /**
     * Get the name of the template.
     */
    public function getName(): string;

    /**
     * Get the directory structure for this template.
     */
    public function getDirectoryStructure(): array;

    /**
     * Get the default files (relative paths and contents) to generate.
     */
    public function getFiles(): array;
}
