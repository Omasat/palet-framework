<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Log;

use Palet\Framework\Log\LogRecord;

/**
 * Amaç: Log verilerini diske/veritabanına yazılmadan önce formatlamak (Örn: JSON, Line).
 */
interface LogFormatterInterface
{
    /**
     * Format the given log record.
     */
    public function format(LogRecord $record): string;
}
