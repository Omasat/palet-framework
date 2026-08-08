<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

interface ViewCompilerInterface
{
    /**
     * Get the path to the compiled version of a view.
     */
    public function getCompiledPath(string $path): string;

    /**
     * Determine if the view at the given path is expired.
     */
    public function isExpired(string $path): bool;

    /**
     * Compile the view at the given path.
     */
    public function compile(string $path): void;
}
