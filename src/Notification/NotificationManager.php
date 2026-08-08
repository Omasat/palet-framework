<?php

declare(strict_types=1);

namespace Palet\Framework\Notification;

use Palet\Framework\Contracts\Notification\NotificationSenderInterface;
use Palet\Framework\Contracts\Notification\NotificationInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Notification\Events\NotificationCreated;

class NotificationManager implements NotificationSenderInterface
{
    public function __construct(
        protected NotificationDispatcher $dispatcher,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function send(mixed $notifiables, NotificationInterface $notification): void
    {
        $notifiables = is_iterable($notifiables) ? $notifiables : [$notifiables];

        foreach ($notifiables as $notifiable) {
            if ($this->events) {
                $this->events->dispatch(new NotificationCreated($notifiable, $notification));
            }
            
            $this->dispatcher->dispatch($notifiable, $notification);
        }
    }
}
