<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Events;

class FileGenerating
{
    public function __construct(
        public readonly string $destinationPath,
        public readonly string $content
    ) {}
}
