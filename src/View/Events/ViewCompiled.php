<?php

declare(strict_types=1);

namespace Palet\Framework\View\Events;

class ViewCompiled
{
    public string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
}
