<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Events;

use Palet\Framework\Generator\GeneratorContext;

class GenerationStarted
{
    public function __construct(public readonly GeneratorContext $context) {}
}
