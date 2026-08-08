<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Kernel;

enum KernelState: string
{
    case Initializing = 'initializing';
    case Bootstrapping = 'bootstrapping';
    case Ready = 'ready';
    case Running = 'running';
    case ShuttingDown = 'shutting_down';
    case Terminated = 'terminated';
    case Failed = 'failed';
}
