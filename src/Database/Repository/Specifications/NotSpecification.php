<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Repository\Specifications;

use Palet\Framework\Contracts\Database\Repository\SpecificationInterface;

class NotSpecification extends CompositeSpecification
{
    protected SpecificationInterface $specification;

    public function __construct(SpecificationInterface $specification)
    {
        $this->specification = $specification;
    }

    public function isSatisfiedBy(object $entity): bool
    {
        return !$this->specification->isSatisfiedBy($entity);
    }
}
