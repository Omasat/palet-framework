<?php

declare(strict_types=1);

namespace Palet\Framework\Notification\Events;

use Palet\Framework\Contracts\Notification\NotificationInterface;

class NotificationCreated
{
    public function __construct(
        public readonly mixed $notifiable,
        public readonly NotificationInterface $notification
    ) {}
}
