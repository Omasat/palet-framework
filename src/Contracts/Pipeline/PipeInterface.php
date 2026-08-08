<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Pipeline;

use Closure;

interface PipeInterface
{
    /**
     * Handle the given passable object and pass it to the next pipe.
     */
    public function handle(mixed $passable, Closure $next): mixed;
}
