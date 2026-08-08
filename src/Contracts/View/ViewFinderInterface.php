<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

interface ViewFinderInterface
{
    /**
     * Get the fully qualified location of the view.
     */
    public function find(string $view): string;

    /**
     * Add a location to the finder.
     */
    public function addLocation(string $location): void;

    /**
     * Add a namespace hint to the finder.
     */
    public function addNamespace(string $namespace, string|array $hints): void;
}
