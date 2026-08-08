<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Log;

use Stringable;

/**
 * Amaç: Farklı log sürücülerinin (File, Database, Syslog vb.) standart yöntemle yazma işlemini yapmasını sağlamak.
 */
interface LogDriverInterface
{
    /**
     * Sürücünün logu kendi mekanizmasına uygun şekilde yazmasını (write) sağlar.
     */
    public function write(string $level, string|Stringable $message, array $context = []): void;
}
