<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Migrations;

interface MigrationInterface
{
    /**
     * Run the migrations.
     */
    public function up(): void;

    /**
     * Reverse the migrations.
     */
    public function down(): void;
}
