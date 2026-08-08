<?php

declare(strict_types=1);

namespace Palet\Framework\Notification\Channels;

use Palet\Framework\Contracts\Notification\ChannelInterface;
use Palet\Framework\Contracts\Notification\NotificationInterface;

class DatabaseChannel implements ChannelInterface
{
    public function send(mixed $notifiable, NotificationInterface $notification, string $content): void
    {
        // Mock inserting notification to DB table
        // error_log("Saving to DB for user: " . ($notifiable->id ?? 'unknown') . " | Content: " . $content);
    }

    public function getName(): string
    {
        return 'database';
    }
}
