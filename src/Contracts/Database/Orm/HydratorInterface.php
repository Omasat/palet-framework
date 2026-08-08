<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm;

interface HydratorInterface
{
    /**
     * Hydrate an array of data into an object.
     */
    public function hydrate(array $data, object $object): object;

    /**
     * Extract data from an object into an array.
     */
    public function extract(object $object): array;
}
