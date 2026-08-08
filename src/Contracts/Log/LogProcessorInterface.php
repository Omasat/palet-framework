<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Log;

use Palet\Framework\Log\LogRecord;

/**
 * Amaç: Log verilerine ekstra meta verileri (Request ID, Memory, Time) eklemek (Process).
 */
interface LogProcessorInterface
{
    /**
     * Process the given log record.
     */
    public function process(LogRecord $record): LogRecord;
}
