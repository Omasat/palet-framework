<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Scaffold;

interface ProjectCreatorInterface
{
    /**
     * Create a new project scaffold at the given path using the specified template.
     */
    public function create(string $targetPath, string $templateName = 'web'): void;
}
