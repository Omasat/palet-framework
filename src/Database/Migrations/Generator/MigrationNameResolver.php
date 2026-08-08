<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations\Generator;

use Palet\Framework\Contracts\Database\Migrations\Generator\MigrationNamingStrategyInterface;

class MigrationNameResolver implements MigrationNamingStrategyInterface
{
    public function analyze(string $name): array
    {
        // Try to match 'create_something_table'
        if (preg_match('/^create_(.*)_table$/i', $name, $matches)) {
            return [
                'table' => $matches[1],
                'create' => true,
            ];
        }

        // Try to match 'add_something_to_something_table' or 'alter_something_table' etc
        if (preg_match('/(?:add|alter|update|remove|drop)_[a-zA-Z0-9_]+_to_(.*)_table$/i', $name, $matches)) {
            return [
                'table' => $matches[1],
                'create' => false,
            ];
        }

        if (preg_match('/(?:add|alter|update|remove|drop)_[a-zA-Z0-9_]+_from_(.*)_table$/i', $name, $matches)) {
            return [
                'table' => $matches[1],
                'create' => false,
            ];
        }

        if (preg_match('/^drop_(.*)_table$/i', $name, $matches)) {
            return [
                'table' => $matches[1],
                'create' => false,
            ];
        }

        // Fallback for simple operations like 'alter_users_table'
        if (preg_match('/^[a-zA-Z0-9_]+_(.*)_table$/i', $name, $matches)) {
            return [
                'table' => $matches[1],
                'create' => false,
            ];
        }

        return [
            'table' => null,
            'create' => false,
        ];
    }
}
