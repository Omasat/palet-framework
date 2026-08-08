<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Events;

use Palet\Framework\Generator\GeneratorContext;

class GenerationFailed
{
    public function __construct(
        public readonly GeneratorContext $context,
        public readonly \Throwable $exception
    ) {}
}
