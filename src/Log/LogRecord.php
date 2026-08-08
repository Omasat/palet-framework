<?php

declare(strict_types=1);

namespace Palet\Framework\Log;

use DateTimeImmutable;
use Stringable;

class LogRecord
{
    public DateTimeImmutable $datetime;
    public string $level;
    public string $message;
    public array $context;

    public function __construct(
        string $level,
        string|Stringable $message,
        array $context = [],
        ?DateTimeImmutable $datetime = null
    ) {
        $this->level = $level;
        $this->message = (string) $message;
        $this->context = $context;
        $this->datetime = $datetime ?? new DateTimeImmutable();
    }
}
