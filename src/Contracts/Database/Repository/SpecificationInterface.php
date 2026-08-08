<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Repository;

interface SpecificationInterface
{
    /**
     * Determine if the given entity satisfies the specification.
     */
    public function isSatisfiedBy(object $entity): bool;

    public function and(SpecificationInterface $specification): SpecificationInterface;
    public function or(SpecificationInterface $specification): SpecificationInterface;
    public function not(): SpecificationInterface;
}
