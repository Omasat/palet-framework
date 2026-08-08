<?php

declare(strict_types=1);

namespace Palet\Framework\Notification;

use Palet\Framework\Contracts\Notification\ChannelInterface;

class NotificationChannelManager
{
    protected array $channels = [];

    public function registerChannel(ChannelInterface $channel): void
    {
        $this->channels[$channel->getName()] = $channel;
    }

    public function getChannel(string $name): ?ChannelInterface
    {
        return $this->channels[$name] ?? null;
    }
}
