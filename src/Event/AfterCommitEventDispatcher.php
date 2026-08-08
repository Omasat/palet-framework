<?php

declare(strict_types=1);

namespace Palet\Framework\Event;

use Palet\Framework\Contracts\Event\EventDispatcherInterface;
use Palet\Framework\Contracts\Event\EventInterface;

class AfterCommitEventDispatcher
{
    protected EventDispatcherInterface $dispatcher;
    protected array $deferredEvents = [];
    protected bool $inTransaction = false;
    
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function beginTransaction(): void
    {
        $this->inTransaction = true;
    }
    
    public function dispatch(EventInterface|string $event, mixed $payload = []): void
    {
        if ($this->inTransaction) {
            $this->deferredEvents[] = [
                'event' => $event,
                'payload' => $payload
            ];
        } else {
            $this->dispatcher->dispatch($event, $payload);
        }
    }
    
    public function commit(): void
    {
        $this->inTransaction = false;
        
        foreach ($this->deferredEvents as $item) {
            $this->dispatcher->dispatch($item['event'], $item['payload']);
        }
        
        $this->deferredEvents = [];
    }
    
    public function rollback(): void
    {
        $this->inTransaction = false;
        $this->deferredEvents = []; // Discard all events
    }
}
