<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Repository\Specifications;

use Palet\Framework\Contracts\Database\Repository\SpecificationInterface;

class OrSpecification extends CompositeSpecification
{
    protected SpecificationInterface $left;
    protected SpecificationInterface $right;

    public function __construct(SpecificationInterface $left, SpecificationInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function isSatisfiedBy(object $entity): bool
    {
        return $this->left->isSatisfiedBy($entity) || $this->right->isSatisfiedBy($entity);
    }
}
