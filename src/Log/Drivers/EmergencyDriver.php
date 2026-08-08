<?php

declare(strict_types=1);

namespace Palet\Framework\Log\Drivers;

use Palet\Framework\Contracts\Log\LogDriverInterface;
use Stringable;

class EmergencyDriver implements LogDriverInterface
{
    public function write(string $level, string|Stringable $message, array $context = []): void
    {
        $contextStr = empty($context) ? '' : json_encode($context);
        $logMessage = sprintf("[%s] %s %s\n", strtoupper($level), (string)$message, $contextStr);
        
        // As a last resort, write to PHP's built-in error log
        error_log($logMessage);
    }
}
