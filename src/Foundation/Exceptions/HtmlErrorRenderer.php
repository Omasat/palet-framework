<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Exceptions;

use Palet\Framework\Contracts\Debug\ExceptionRendererInterface;
use Throwable;

class HtmlErrorRenderer implements ExceptionRendererInterface
{
    protected StackTraceFormatter $formatter;

    public function __construct(StackTraceFormatter $formatter = null)
    {
        $this->formatter = $formatter ?? new StackTraceFormatter();
    }

    public function render(Throwable $e, bool $debug): string
    {
        if (!$debug) {
            return "<html><body><h1>500 Server Error</h1><p>An unexpected error occurred.</p></body></html>";
        }

        $class = get_class($e);
        $message = htmlspecialchars($e->getMessage());
        $file = htmlspecialchars($e->getFile());
        $line = $e->getLine();
        
        $trace = $this->formatter->format($e);
        $traceHtml = implode("<br>", array_map('htmlspecialchars', $trace));

        return <<<HTML
        <html>
        <head>
            <title>Error: {$class}</title>
            <style>
                body { font-family: sans-serif; background: #f9f9f9; padding: 20px; }
                .error-box { background: #fff; border-left: 5px solid #e74c3c; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #e74c3c; margin-top: 0; }
                .file-info { color: #7f8c8d; font-size: 0.9em; margin-bottom: 20px; }
                .trace { background: #2c3e50; color: #ecf0f1; padding: 15px; overflow-x: auto; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>{$class}</h1>
                <p><strong>Message:</strong> {$message}</p>
                <div class="file-info">in {$file} on line {$line}</div>
                <h3>Stack Trace</h3>
                <div class="trace">{$traceHtml}</div>
            </div>
        </body>
        </html>
        HTML;
    }
}
