<?php

declare(strict_types=1);

namespace Palet\Framework\Notification\State;

enum DeliveryStatus: string
{
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case SENDING = 'sending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
