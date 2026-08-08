<?php

declare(strict_types=1);

namespace Palet\Framework\Translation;

class MessageFormatter
{
    public function format(string $message, array $replace = [], ?string $locale = null): string
    {
        if (empty($replace)) {
            return $message;
        }

        $replace = $this->sortReplacements($replace);

        foreach ($replace as $key => $value) {
            $value = (string) $value;
            $message = str_replace(
                [':' . $key, ':' . strtoupper($key), ':' . ucfirst($key)],
                [$value, strtoupper($value), ucfirst($value)],
                $message
            );
        }

        return $message;
    }

    public function choice(string $message, int|float $number, array $replace = [], ?string $locale = null): string
    {
        $segments = explode('|', $message);
        
        $extracted = $this->extractPlural($segments, $number);

        if ($extracted !== null) {
            $replace['count'] = $number;
            return $this->format($extracted, $replace, $locale);
        }

        return $message;
    }

    protected function sortReplacements(array $replace): array
    {
        uksort($replace, function ($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });

        return $replace;
    }

    protected function extractPlural(array $segments, int|float $number): ?string
    {
        // Simple pluralization logic for matching like Laravel's pluralization:
        // {0} There are none|[1,19] There are some|[20,*] There are many
        // Or simple string1|string2
        
        foreach ($segments as $segment) {
            if (preg_match('/^\{(\d+)\}\s*(.*)/', $segment, $matches)) {
                if ((float) $matches[1] === (float) $number) {
                    return $matches[2];
                }
            } elseif (preg_match('/^\[([\d\*]+),([\d\*]+)\]\s*(.*)/', $segment, $matches)) {
                $min = $matches[1] === '*' ? -INF : (float) $matches[1];
                $max = $matches[2] === '*' ? INF : (float) $matches[2];
                
                if ($number >= $min && $number <= $max) {
                    return $matches[3];
                }
            }
        }

        // Fallback to simple pluralization: if array has 2 elements, first is for 1, second is for others
        if (count($segments) === 2) {
            return $number === 1 ? $segments[0] : $segments[1];
        }
        
        if (count($segments) > 2) {
            return $number === 1 ? $segments[0] : ($number === 0 || $number > 1 ? $segments[1] : $segments[2]);
        }

        return $segments[0] ?? null;
    }
}
