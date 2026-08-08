<?php

declare(strict_types=1);

namespace Palet\Framework\Log\Drivers;

use Palet\Framework\Contracts\Log\LogDriverInterface;
use Palet\Framework\Contracts\Log\LogFormatterInterface;
use Palet\Framework\Log\Formatters\LineFormatter;
use Palet\Framework\Log\LogRecord;
use Stringable;

class DailyFileDriver implements LogDriverInterface
{
    protected string $path;
    protected int $days;
    protected LogFormatterInterface $formatter;

    public function __construct(string $path, int $days = 7, ?LogFormatterInterface $formatter = null)
    {
        $this->path = $path;
        $this->days = $days;
        $this->formatter = $formatter ?? new LineFormatter();
    }

    public function write(string $level, string|Stringable $message, array $context = []): void
    {
        $record = new LogRecord($level, $message, $context);
        $formatted = $this->formatter->format($record);
        
        $filePath = $this->getDailyPath();

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($filePath, $formatted, FILE_APPEND | LOCK_EX);

        $this->rotate();
    }

    protected function getDailyPath(): string
    {
        $info = pathinfo($this->path);
        $date = date('Y-m-d');
        
        $filename = ($info['filename'] ?? 'palet') . '-' . $date;
        $extension = isset($info['extension']) ? '.' . $info['extension'] : '.log';
        
        return ($info['dirname'] ?? '') . DIRECTORY_SEPARATOR . $filename . $extension;
    }

    protected function rotate(): void
    {
        if ($this->days <= 0) {
            return;
        }

        $info = pathinfo($this->path);
        $dirname = $info['dirname'] ?? '';
        $filename = $info['filename'] ?? 'palet';

        if (!is_dir($dirname)) {
            return;
        }

        $files = glob($dirname . DIRECTORY_SEPARATOR . $filename . '-*.*');
        if (!$files) {
            return;
        }

        $threshold = strtotime("-{$this->days} days");

        foreach ($files as $file) {
            if (preg_match('/-(\d{4}-\d{2}-\d{2})\./', $file, $matches)) {
                $fileDate = strtotime($matches[1]);
                if ($fileDate !== false && $fileDate < $threshold) {
                    @unlink($file);
                }
            }
        }
    }
}
