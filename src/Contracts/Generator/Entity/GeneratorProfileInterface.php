<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Entity;

interface GeneratorProfileInterface
{
    public function getName(): string;
    
    /**
     * Returns an array of components to generate by default.
     * e.g. ['entity', 'repository', 'dto']
     */
    public function getDefaultComponents(): array;
}
