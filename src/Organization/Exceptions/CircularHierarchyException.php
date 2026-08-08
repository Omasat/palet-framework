<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Exceptions;

use RuntimeException;

class CircularHierarchyException extends RuntimeException
{
    // Thrown when a circular dependency is detected in the organization tree
}
