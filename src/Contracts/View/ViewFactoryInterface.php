<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

interface ViewFactoryInterface
{
    /**
     * Determine if a given view exists.
     */
    public function exists(string $view): bool;

    /**
     * Get the evaluated view contents for the given view.
     */
    public function make(string $view, array $data = []): ViewInterface;

    /**
     * Add a piece of shared data to the environment.
     */
    public function share(string|array $key, mixed $value = null): mixed;
}
