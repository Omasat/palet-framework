<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator;

use Palet\Framework\Generator\GeneratorContext;

interface GeneratorInterface
{
    /**
     * Generate code based on the given context.
     * Returns true if successful, false otherwise.
     */
    public function generate(GeneratorContext $context): bool;
}
