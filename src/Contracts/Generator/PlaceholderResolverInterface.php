<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator;

interface PlaceholderResolverInterface
{
    /**
     * Resolve placeholders in a string using context variables.
     */
    public function resolve(string $content, array $variables): string;
}
