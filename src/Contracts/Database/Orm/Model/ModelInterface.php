<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\Model;

interface ModelInterface
{
    /**
     * Save the model to the database.
     */
    public function save(): bool;

    /**
     * Delete the model from the database.
     */
    public function delete(): bool;
    
    /**
     * Get the value of the model's primary key.
     */
    public function getKey(): mixed;
}
