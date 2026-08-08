<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Debug;

use Throwable;

interface ExceptionRendererInterface
{
    /**
     * Render the exception to a string.
     */
    public function render(Throwable $e, bool $debug): string;
}
