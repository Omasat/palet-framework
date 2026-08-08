<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Exceptions;

use RuntimeException;

class InvalidHierarchyException extends RuntimeException
{
    // Thrown when trying to add a node to an invalid parent (e.g. Branch to Team)
}
