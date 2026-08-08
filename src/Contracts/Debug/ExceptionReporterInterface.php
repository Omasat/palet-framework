<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Debug;

use Throwable;

interface ExceptionReporterInterface
{
    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void;
}
