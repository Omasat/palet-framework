<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription\Events;

use Palet\Framework\Contracts\Subscription\LicenseInterface;

class LicenseValidated
{
    public function __construct(public readonly LicenseInterface $license) {}
}
