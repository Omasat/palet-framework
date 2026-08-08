<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Entity;

interface EntityGeneratorInterface
{
    /**
     * Generate bulk entity files based on context and profile.
     */
    public function generateBulk(string $entityName, array $options = []): void;
}
