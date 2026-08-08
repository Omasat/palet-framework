<?php

declare(strict_types=1);

namespace Palet\Framework\Container;

use Closure;

readonly class Binding
{
    public function __construct(
        public Closure|string $concrete,
        public bool $shared
    ) {
    }
}
