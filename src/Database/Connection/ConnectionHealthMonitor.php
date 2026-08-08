<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Connection;

use PDO;
use Exception;

class ConnectionHealthMonitor
{
    /**
     * Check if the PDO connection is still alive.
     */
    public function isAlive(PDO $pdo): bool
    {
        try {
            // A simple query to check the connection. SQLite uses SELECT 1, MySQL/PgSQL can too.
            $pdo->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
