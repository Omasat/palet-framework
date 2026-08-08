<?php

declare(strict_types=1);

namespace Palet\Framework\Feature\State;

enum FeatureState: string
{
    case DRAFT = 'draft';
    case DISABLED = 'disabled';
    case ENABLED = 'enabled';
    case SCHEDULED = 'scheduled';
    case DEPRECATED = 'deprecated';
    case ARCHIVED = 'archived';
}
