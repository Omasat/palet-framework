<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Observer;

class ObserverManager
{
    protected array $observers = [];

    public function register(string $modelClass, object $observer): static
    {
        $this->observers[$modelClass][] = $observer;
        return $this;
    }

    public function dispatch(string $modelClass, string $event, object $model): void
    {
        $observers = $this->observers[$modelClass] ?? [];
        
        foreach ($observers as $observer) {
            if (method_exists($observer, $event)) {
                $observer->{$event}($model);
            }
        }
    }
}
