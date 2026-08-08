<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Provisioning\State;

enum TenantState: string
{
    case PENDING = 'pending';
    case PROVISIONING = 'provisioning';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case ARCHIVED = 'archived';
    case MAINTENANCE = 'maintenance';
    case DELETED = 'deleted';
}
