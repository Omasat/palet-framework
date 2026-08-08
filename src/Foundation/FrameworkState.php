<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation;

/**
 * Framework durumlarını temsil eden Enum.
 */
enum FrameworkState
{
    case Booting;
    case Bootstrapping;
    case RegisteringProviders;
    case BootingProviders;
    case Ready;
    case Maintenance;
    case Error;
    case Terminated;
}
