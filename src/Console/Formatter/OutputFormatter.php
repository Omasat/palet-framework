<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Formatter;

class OutputFormatter
{
    // Basic ANSI colors
    protected array $styles = [
        'info'    => ['set' => '32', 'unset' => '39'], // Green
        'error'   => ['set' => '31', 'unset' => '39'], // Red
        'comment' => ['set' => '33', 'unset' => '39'], // Yellow
        'question'=> ['set' => '36', 'unset' => '39'], // Cyan
    ];

    public function format(string $message): string
    {
        foreach ($this->styles as $tag => $codes) {
            $message = preg_replace_callback("/<{$tag}>(.*?)<\/{$tag}>/s", function ($matches) use ($codes) {
                return sprintf("\033[%sm%s\033[%sm", $codes['set'], $matches[1], $codes['unset']);
            }, $message);
        }

        return $message;
    }
}
