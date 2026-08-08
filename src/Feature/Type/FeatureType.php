<?php

declare(strict_types=1);

namespace Palet\Framework\Feature\Type;

enum FeatureType: string
{
    case BOOLEAN = 'boolean';
    case PERCENTAGE = 'percentage';
    case TENANT = 'tenant';
    case PLAN = 'plan';
    case USER = 'user';
    case ROLE = 'role';
    case ENVIRONMENT = 'environment';
    case TIME_BASED = 'time_based';
}
