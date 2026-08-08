<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Module;

interface ModuleGeneratorInterface
{
    /**
     * Generates a new module structure.
     */
    public function generate(string $name, array $options = []): void;
}
