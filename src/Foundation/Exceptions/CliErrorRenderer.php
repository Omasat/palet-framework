<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Exceptions;

use Palet\Framework\Contracts\Debug\ExceptionRendererInterface;
use Throwable;

class CliErrorRenderer implements ExceptionRendererInterface
{
    protected StackTraceFormatter $formatter;

    public function __construct(StackTraceFormatter $formatter = null)
    {
        $this->formatter = $formatter ?? new StackTraceFormatter();
    }

    public function render(Throwable $e, bool $debug): string
    {
        if (!$debug) {
            return "\033[31m[Error] 500 Server Error - An unexpected error occurred.\033[0m\n";
        }

        $class = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        
        $trace = $this->formatter->format($e);
        $traceStr = implode("\n", $trace);

        return "\n\033[41;37m {$class} \033[0m\n\n" .
               "\033[33mMessage:\033[0m {$message}\n" .
               "\033[33mFile:\033[0m {$file}:{$line}\n\n" .
               "\033[36mStack Trace:\033[0m\n{$traceStr}\n\n";
    }
}
