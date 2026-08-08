<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database;

use PDO;

interface ConnectionPoolInterface
{
    /**
     * Get an active connection from the pool.
     */
    public function checkout(string $name, array $config): PDO;

    /**
     * Release a connection back to the pool.
     */
    public function checkin(string $name, PDO $connection): void;
    
    /**
     * Remove all connections from the pool.
     */
    public function flush(): void;
}
