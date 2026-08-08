<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Events;

interface SubscriberInterface
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param EventDispatcherInterface $events
     */
    public function subscribe(EventDispatcherInterface $events): void;
}
