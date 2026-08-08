<?php

declare(strict_types=1);

namespace Palet\Framework\Session\Drivers;

use SessionHandlerInterface;

class ArraySessionDriver implements SessionHandlerInterface
{
    protected array $storage = [];
    protected int $minutes;

    public function __construct(int $minutes)
    {
        $this->minutes = $minutes;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        if (isset($this->storage[$id])) {
            if ($this->storage[$id]['time'] >= time() - ($this->minutes * 60)) {
                return $this->storage[$id]['data'];
            }
        }
        return '';
    }

    public function write(string $id, string $data): bool
    {
        $this->storage[$id] = [
            'data' => $data,
            'time' => time(),
        ];
        return true;
    }

    public function destroy(string $id): bool
    {
        if (isset($this->storage[$id])) {
            unset($this->storage[$id]);
        }
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $deleted = 0;
        $time = time();
        foreach ($this->storage as $id => $session) {
            if ($session['time'] < $time - $max_lifetime) {
                unset($this->storage[$id]);
                $deleted++;
            }
        }
        return $deleted;
    }
}
