<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class AsCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $description = ''
    ) {
    }
}
