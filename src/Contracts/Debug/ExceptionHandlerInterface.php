<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Debug;

use Throwable;

interface ExceptionHandlerInterface
{
    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void;

    /**
     * Render an exception into an HTTP response string or array.
     */
    public function render(Throwable $e): string|array;

    /**
     * Render an exception to the console.
     */
    public function renderForConsole(Throwable $e): void;
}
