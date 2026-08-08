<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Repository;

use Palet\Framework\Contracts\Database\Repository\CriteriaInterface;

class CriteriaPipeline
{
    /** @var CriteriaInterface[] */
    protected array $criteria = [];

    public function push(CriteriaInterface $criteria): static
    {
        $this->criteria[] = $criteria;
        return $this;
    }

    public function clear(): void
    {
        $this->criteria = [];
    }

    public function apply(mixed $query): mixed
    {
        foreach ($this->criteria as $criteria) {
            $query = $criteria->apply($query);
        }

        return $query;
    }
}
