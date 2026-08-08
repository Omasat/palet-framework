<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Model;

use DateTime;
use DateTimeImmutable;

class AttributeCaster
{
    public function cast(string $key, mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'int', 'integer' => (int) $value,
            'real', 'float', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => (bool) $value,
            'array' => json_decode($value, true),
            'object' => json_decode($value, false),
            'json' => json_decode($value, true),
            'datetime' => $this->asDateTime($value),
            'immutable_datetime' => $this->asDateTimeImmutable($value),
            default => $value,
        };
    }
    
    public function uncast(string $key, mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'array', 'object', 'json' => json_encode($value),
            'datetime', 'immutable_datetime' => $this->fromDateTime($value),
            default => $value,
        };
    }

    protected function asDateTime(mixed $value): DateTime
    {
        if ($value instanceof DateTime) {
            return $value;
        }

        if ($value instanceof DateTimeImmutable) {
            return DateTime::createFromImmutable($value);
        }

        if (is_numeric($value)) {
            $date = new DateTime();
            $date->setTimestamp((int) $value);
            return $date;
        }

        return new DateTime($value);
    }
    
    protected function asDateTimeImmutable(mixed $value): DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($value);
        }

        if (is_numeric($value)) {
            $date = new DateTimeImmutable();
            return $date->setTimestamp((int) $value);
        }

        return new DateTimeImmutable($value);
    }
    
    protected function fromDateTime(mixed $value): string
    {
        return $value instanceof DateTime || $value instanceof DateTimeImmutable 
            ? $value->format('Y-m-d H:i:s') 
            : (string) $value;
    }
}
