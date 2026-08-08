<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database;

use PDO;

interface DriverInterface
{
    /**
     * Create a new PDO connection instance.
     */
    public function connect(array $config): PDO;

    /**
     * Get the DSN string for the PDO connection.
     */
    public function getDsn(array $config): string;
}
