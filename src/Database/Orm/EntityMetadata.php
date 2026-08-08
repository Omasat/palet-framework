<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm;

use Palet\Framework\Contracts\Database\Orm\MetadataInterface;

class EntityMetadata implements MetadataInterface
{
    protected string $className;
    protected string $tableName;
    protected string $primaryKey;

    public function __construct(string $className, string $tableName, string $primaryKey = 'id')
    {
        $this->className = $className;
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }
    
    public function getClassName(): string
    {
        return $this->className;
    }
}
