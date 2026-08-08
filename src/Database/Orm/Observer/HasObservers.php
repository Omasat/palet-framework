<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Observer;

trait HasObservers
{
    protected static ObserverManager $observerManager;
    
    public static function setObserverManager(ObserverManager $manager): void
    {
        static::$observerManager = $manager;
    }
    
    public static function observe(object $observer): void
    {
        if (isset(static::$observerManager)) {
            static::$observerManager->register(static::class, $observer);
        }
    }

    public function fireModelEvent(string $event): void
    {
        if (isset(static::$observerManager)) {
            static::$observerManager->dispatch(static::class, $event, $this);
        }
    }
}
