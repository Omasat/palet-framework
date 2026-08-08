<?php

declare(strict_types=1);

namespace Palet\Framework\Feature\Events;

use Palet\Framework\Contracts\Feature\CapabilityInterface;

class CapabilityRevoked
{
    public function __construct(public readonly CapabilityInterface $capability) {}
}
