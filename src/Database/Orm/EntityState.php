<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm;

enum EntityState
{
    case NEW;
    case MANAGED;
    case DIRTY;
    case REMOVED;
    case DETACHED;
}
