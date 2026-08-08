<?php

declare(strict_types=1);

namespace Palet\Framework\Notification;

class NotificationPreferences
{
    protected array $userPreferences = [];

    /**
     * Set a user's channel preference (e.g. ['mail' => true, 'sms' => false])
     */
    public function setPreferences(string|int $userId, array $preferences): void
    {
        $this->userPreferences[$userId] = $preferences;
    }

    public function shouldSendVia(string|int $userId, string $channel): bool
    {
        return $this->userPreferences[$userId][$channel] ?? true;
    }
}
