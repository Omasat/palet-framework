<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Module;

interface ModuleRegistrarInterface
{
    /**
     * Get a list of all registered modules.
     */
    public function all(): array;

    /**
     * Enable a specific module.
     */
    public function enable(string $name): bool;

    /**
     * Disable a specific module.
     */
    public function disable(string $name): bool;
}
