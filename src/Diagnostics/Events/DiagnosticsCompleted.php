<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics\Events;

class DiagnosticsCompleted
{
    public function __construct(public readonly array $results) {}
}
