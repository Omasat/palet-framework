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
            return <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8"><title>500 - Server Error</title>
                <script src="https://cdn.tailwindcss.com"></script>
            </head>
            <body class="bg-slate-900 text-white h-screen flex items-center justify-center font-sans">
                <div class="text-center space-y-4">
                    <h1 class="text-6xl font-bold text-slate-700">500</h1>
                    <p class="text-xl text-slate-400">Beklenmedik bir sunucu hatası oluştu.</p>
                </div>
            </body>
            </html>
            HTML;
        }

        $class = get_class($e);
        $message = htmlspecialchars($e->getMessage());
        $file = htmlspecialchars($e->getFile());
        $line = $e->getLine();
        
        $trace = $this->formatter->format($e);
        $traceHtml = implode("\n", array_map('htmlspecialchars', $trace));

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Palet Error: {$class}</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-slate-950 text-slate-300 font-sans p-8">
            <div class="max-w-6xl mx-auto space-y-6">
                <!-- Header -->
                <div class="bg-red-500/10 border border-red-500/20 p-6 rounded-2xl">
                    <div class="text-red-400 text-sm font-semibold tracking-wide uppercase mb-1">Palet Framework Exception</div>
                    <h1 class="text-3xl font-bold text-red-500 break-words mb-4">{$class}</h1>
                    <p class="text-2xl text-slate-200 font-light break-words">"{$message}"</p>
                </div>
                
                <!-- File Info -->
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl flex items-center gap-4">
                    <div class="bg-slate-800 p-3 rounded-lg text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500 uppercase font-semibold mb-1">Dosya Konumu</div>
                        <div class="text-lg font-mono text-blue-400">{$file} <span class="text-slate-500">satır</span> <span class="text-emerald-400 font-bold">{$line}</span></div>
                    </div>
                </div>

                <!-- Stack Trace -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50">
                        <h3 class="font-semibold text-slate-300">Stack Trace (Çağrı Yığını)</h3>
                    </div>
                    <div class="p-6 overflow-x-auto">
                        <pre class="font-mono text-sm leading-relaxed text-slate-400">{$traceHtml}</pre>
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
