<?php

declare(strict_types=1);

namespace Palet\Framework\Notification;

use Palet\Framework\Contracts\Notification\NotificationInterface;
use Palet\Framework\Contracts\Notification\TemplateInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Notification\Events\NotificationSent;

class NotificationDispatcher
{
    public function __construct(
        protected NotificationChannelManager $channelManager,
        protected TemplateInterface $templateEngine,
        protected NotificationPreferences $preferences,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function dispatch(mixed $notifiable, NotificationInterface $notification): void
    {
        $userId = $notifiable->id ?? 0;
        $channels = $notification->getChannels($notifiable);
        
        $content = $this->templateEngine->render(
            $notification->getTemplateName(),
            $notification->getTemplateData()
        );

        foreach ($channels as $channelName) {
            // Check user preferences
            if (!$this->preferences->shouldSendVia($userId, $channelName)) {
                continue;
            }

            $channel = $this->channelManager->getChannel($channelName);
            if ($channel) {
                $channel->send($notifiable, $notification, $content);
                
                if ($this->events) {
                    $this->events->dispatch(new NotificationSent($notifiable, $notification, $channelName));
                }
            }
        }
    }
}
