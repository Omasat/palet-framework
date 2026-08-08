<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Queue;

interface JobInterface
{
    public function handle(): void;
    
    public function getId(): string;
    
    public function getQueue(): string;
    
    public function setQueue(string $queue): void;

    public function getDelay(): int;
    
    public function setDelay(int $delay): void;

    public function getMaxTries(): int;
    
    public function getAttempts(): int;
    
    public function incrementAttempts(): void;

    public function release(int $delay = 0): void;
    
    public function markAsFailed(\Throwable $exception): void;
}
