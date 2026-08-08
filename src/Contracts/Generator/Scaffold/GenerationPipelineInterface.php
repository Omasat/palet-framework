<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Scaffold;

interface GenerationPipelineInterface
{
    /**
     * Process an ordered list of steps to generate.
     */
    public function process(array $steps, array $options = []): void;
}
