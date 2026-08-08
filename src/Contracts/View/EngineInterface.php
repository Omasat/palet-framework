<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

interface EngineInterface
{
    /**
     * Get the evaluated contents of the view.
     */
    public function get(string $path, array $data = []): string;
}
