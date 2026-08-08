<?php

declare(strict_types=1);

namespace Palet\Framework\Log\Formatters;

use Palet\Framework\Contracts\Log\LogFormatterInterface;
use Palet\Framework\Log\LogRecord;

class JsonFormatter implements LogFormatterInterface
{
    protected string $dateFormat;

    public function __construct(string $dateFormat = 'c')
    {
        $this->dateFormat = $dateFormat;
    }

    public function format(LogRecord $record): string
    {
        $data = [
            'datetime' => $record->datetime->format($this->dateFormat),
            'level' => strtoupper($record->level),
            'message' => $record->message,
        ];

        if (!empty($record->context)) {
            $data['context'] = $record->context;
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
}
