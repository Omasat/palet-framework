<?php

declare(strict_types=1);

namespace Palet\Framework\Log\Drivers;

use Palet\Framework\Contracts\Log\LogDriverInterface;
use Palet\Framework\Contracts\Log\LogFormatterInterface;
use Palet\Framework\Log\Formatters\LineFormatter;
use Palet\Framework\Log\LogRecord;
use Stringable;

class FileDriver implements LogDriverInterface
{
    protected string $path;
    protected LogFormatterInterface $formatter;

    public function __construct(string $path, ?LogFormatterInterface $formatter = null)
    {
        $this->path = $path;
        $this->formatter = $formatter ?? new LineFormatter();
    }

    public function write(string $level, string|Stringable $message, array $context = []): void
    {
        $record = new LogRecord($level, $message, $context);
        $formatted = $this->formatter->format($record);

        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($this->path, $formatted, FILE_APPEND | LOCK_EX);
    }
}
