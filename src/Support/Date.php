<?php

declare(strict_types=1);

namespace Palet\Framework\Support;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

class Date extends DateTimeImmutable
{
    public static function now(?string $timezone = null): static
    {
        return new static('now', $timezone ? new DateTimeZone($timezone) : null);
    }

    public static function today(?string $timezone = null): static
    {
        return static::now($timezone)->setTime(0, 0, 0);
    }

    public static function tomorrow(?string $timezone = null): static
    {
        return static::today($timezone)->addDays(1);
    }

    public static function yesterday(?string $timezone = null): static
    {
        return static::today($timezone)->subDays(1);
    }

    public function addDays(int $days): static
    {
        return $this->modify("+{$days} days");
    }

    public function subDays(int $days): static
    {
        return $this->modify("-{$days} days");
    }
}
