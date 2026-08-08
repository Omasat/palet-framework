<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Output;

use Palet\Framework\Contracts\Console\OutputInterface;
use Palet\Framework\Console\Formatter\OutputFormatter;

class ConsoleOutput implements OutputInterface
{
    protected OutputFormatter $formatter;
    
    // Test environments will buffer output
    protected bool $buffered = false;
    protected string $buffer = '';

    public function __construct(?OutputFormatter $formatter = null)
    {
        $this->formatter = $formatter ?? new OutputFormatter();
    }

    public function setBuffered(bool $buffered): void
    {
        $this->buffered = $buffered;
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function write(string|array $messages, bool $newline = false): void
    {
        $messages = is_iterable($messages) ? $messages : [$messages];

        foreach ($messages as $message) {
            $formatted = $this->formatter->format($message);
            $output = $formatted . ($newline ? PHP_EOL : '');
            
            if ($this->buffered) {
                $this->buffer .= $output;
            } else {
                echo $output;
            }
        }
    }

    public function writeln(string|array $messages): void
    {
        $this->write($messages, true);
    }
}
