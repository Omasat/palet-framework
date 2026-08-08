<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Repository\Specifications;

use Palet\Framework\Contracts\Database\Repository\SpecificationInterface;

abstract class CompositeSpecification implements SpecificationInterface
{
    public function and(SpecificationInterface $specification): SpecificationInterface
    {
        return new AndSpecification($this, $specification);
    }

    public function or(SpecificationInterface $specification): SpecificationInterface
    {
        return new OrSpecification($this, $specification);
    }

    public function not(): SpecificationInterface
    {
        return new NotSpecification($this);
    }
}
