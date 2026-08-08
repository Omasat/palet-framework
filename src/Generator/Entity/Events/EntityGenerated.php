<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Entity\Events;

class EntityGenerated
{
    public function __construct(
        public readonly string $entityName,
        public readonly string $componentType,
        public readonly string $destinationPath
    ) {}
}
