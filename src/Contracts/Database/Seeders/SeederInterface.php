<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Seeders;

interface SeederInterface
{
    /**
     * Run the database seeds.
     */
    public function run(): void;

    /**
     * Call another seeder.
     */
    public function call(array|string $class): void;
}
