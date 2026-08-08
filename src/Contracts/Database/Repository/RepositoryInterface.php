<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Repository;

interface RepositoryInterface extends ReadRepositoryInterface, WriteRepositoryInterface
{
    public function getModelClass(): string;
}
