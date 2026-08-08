<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Jobs;

use Palet\Framework\Contracts\Queue\JobInterface;

abstract class Job implements JobInterface
{
    protected bool $deleted = false;
    protected bool $released = false;

    public function delete(): void
    {
        $this->deleted = true;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function release(int $delay = 0): void
    {
        $this->released = true;
    }

    public function isReleased(): bool
    {
        return $this->released;
    }

    protected function resolveAndFire(array $payload): void
    {
        $class = $payload['class'] ?? null;
        $method = $payload['method'] ?? 'handle';

        if (!$class || !class_exists($class)) {
            throw new \RuntimeException("Job class [{$class}] not found.");
        }

        $data = $payload['data'] ?? [];
        if (!is_array($data)) {
            $data = [$data];
        }

        $instance = new $class(...$data);
        $instance->{$method}();
    }
}
