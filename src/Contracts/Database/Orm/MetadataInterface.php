<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm;

interface MetadataInterface
{
    /**
     * Get the table name for the entity.
     */
    public function getTableName(): string;

    /**
     * Get the primary key column name for the entity.
     */
    public function getPrimaryKey(): string;
    
    /**
     * Get the class name of the entity.
     */
    public function getClassName(): string;
}
