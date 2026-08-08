<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\Relations;

use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;

interface PivotInterface extends ModelInterface
{
    /**
     * Get the foreign key of the parent model.
     */
    public function getForeignKey(): string;

    /**
     * Get the foreign key of the related model.
     */
    public function getRelatedKey(): string;
}
