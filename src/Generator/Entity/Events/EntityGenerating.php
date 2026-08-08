<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Entity\Events;

class EntityGenerating
{
    public function __construct(
        public readonly string $entityName,
        public readonly string $componentType
    ) {}
}
