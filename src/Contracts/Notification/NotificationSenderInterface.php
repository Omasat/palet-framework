<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Notification;

interface NotificationSenderInterface
{
    public function send(mixed $notifiables, NotificationInterface $notification): void;
}
