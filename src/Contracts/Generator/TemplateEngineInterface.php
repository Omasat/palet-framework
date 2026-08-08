<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator;

interface TemplateEngineInterface
{
    /**
     * Compile a template stub with the given variables.
     */
    public function compile(string $templateContent, array $variables): string;
}
