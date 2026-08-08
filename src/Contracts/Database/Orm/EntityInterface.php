<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm;

interface EntityInterface
{
    // Marker interface for entities. 
    // In Data Mapper, entities can be plain PHP objects (POPOs),
    // but having a marker interface can be useful for type hinting in certain places.
}
