<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Events;

use Palet\Framework\Contracts\Organization\OrganizationInterface;

class OrganizationCreated
{
    public function __construct(public readonly OrganizationInterface $organization) {}
}
