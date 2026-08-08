<?php

declare(strict_types=1);

namespace Palet\Framework\Log\Formatters;

use Palet\Framework\Contracts\Log\LogFormatterInterface;
use Palet\Framework\Log\LogRecord;

class LineFormatter implements LogFormatterInterface
{
    protected string $format;
    protected string $dateFormat;

    public function __construct(
        string $format = "[%datetime%] %level_name%: %message% %context%\n",
        string $dateFormat = 'Y-m-d H:i:s'
    ) {
        $this->format = $format;
        $this->dateFormat = $dateFormat;
    }

    public function format(LogRecord $record): string
    {
        $contextStr = empty($record->context) ? '' : json_encode($record->context, JSON_UNESCAPED_UNICODE);

        return str_replace(
            ['%datetime%', '%level_name%', '%message%', '%context%'],
            [
                $record->datetime->format($this->dateFormat),
                strtoupper($record->level),
                $record->message,
                $contextStr
            ],
            $this->format
        );
    }
}
