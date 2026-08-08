<?php

declare(strict_types=1);

namespace Palet\Framework\Notification\Channels;

use Palet\Framework\Contracts\Notification\ChannelInterface;
use Palet\Framework\Contracts\Notification\NotificationInterface;

class MailChannel implements ChannelInterface
{
    public function send(mixed $notifiable, NotificationInterface $notification, string $content): void
    {
        // Mock sending email
        // error_log("Sending Mail to: " . ($notifiable->email ?? 'unknown') . " | Content: " . $content);
    }

    public function getName(): string
    {
        return 'mail';
    }
}
