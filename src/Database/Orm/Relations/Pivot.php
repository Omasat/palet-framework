<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Relations;

use Palet\Framework\Database\Orm\Model\BaseModel;
use Palet\Framework\Contracts\Database\Orm\Relations\PivotInterface;

class Pivot extends BaseModel implements PivotInterface
{
    protected string $foreignKey;
    protected string $relatedKey;

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getRelatedKey(): string
    {
        return $this->relatedKey;
    }
    
    public function setKeys(string $foreignKey, string $relatedKey): static
    {
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;
        return $this;
    }
}
