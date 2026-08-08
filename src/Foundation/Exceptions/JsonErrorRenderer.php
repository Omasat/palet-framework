<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Exceptions;

use Palet\Framework\Contracts\Debug\ExceptionRendererInterface;
use Throwable;

class JsonErrorRenderer implements ExceptionRendererInterface
{
    protected StackTraceFormatter $formatter;

    public function __construct(StackTraceFormatter $formatter = null)
    {
        $this->formatter = $formatter ?? new StackTraceFormatter();
    }

    public function render(Throwable $e, bool $debug): string
    {
        if (!$debug) {
            return json_encode([
                'error' => [
                    'message' => '500 Server Error',
                ]
            ], JSON_PRETTY_PRINT);
        }

        return json_encode([
            'error' => [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $this->formatter->format($e),
            ]
        ], JSON_PRETTY_PRINT);
    }
}
