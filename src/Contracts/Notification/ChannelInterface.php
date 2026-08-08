<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Notification;

interface ChannelInterface
{
    public function send(mixed $notifiable, NotificationInterface $notification, string $content): void;
    public function getName(): string;
}
