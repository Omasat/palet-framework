<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Event;

interface SubscriberInterface
{
    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(EventDispatcherInterface $events): void;
}
