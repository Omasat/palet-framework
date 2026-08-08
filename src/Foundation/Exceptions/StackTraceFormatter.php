<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Exceptions;

use Throwable;

class StackTraceFormatter
{
    protected SecurityMasker $masker;

    public function __construct(SecurityMasker $masker = null)
    {
        $this->masker = $masker ?? new SecurityMasker();
    }

    /**
     * Formats the exception trace into a safe, readable string array.
     *
     * @param Throwable $e
     * @return array<int, string>
     */
    public function format(Throwable $e): array
    {
        $formatted = [];
        $trace = $e->getTrace();

        foreach ($trace as $index => $frame) {
            $file = $frame['file'] ?? '[internal function]';
            $line = $frame['line'] ?? '';
            $class = $frame['class'] ?? '';
            $type = $frame['type'] ?? '';
            $function = $frame['function'] ?? '';

            // Mask args if any
            $args = '';
            if (isset($frame['args']) && is_array($frame['args'])) {
                // Sadece tip bilgisini gösteriyoruz, değerleri maskeliyoruz
                $maskedArgs = array_map(function ($arg) {
                    if (is_object($arg)) {
                        return 'Object(' . get_class($arg) . ')';
                    }
                    if (is_array($arg)) {
                        return 'Array';
                    }
                    if (is_string($arg)) {
                        return "'[MASKED_STRING]'";
                    }
                    return gettype($arg);
                }, $frame['args']);
                
                $args = implode(', ', $maskedArgs);
            }

            $lineStr = $line ? ":$line" : '';
            $formatted[] = "#$index $file$lineStr - $class$type$function($args)";
        }

        return $formatted;
    }
}
