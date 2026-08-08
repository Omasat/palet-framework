<?php

declare(strict_types=1);

namespace Palet\Framework\Session;

use Palet\Framework\Contracts\Session\SessionInterface;
use SessionHandlerInterface;
use Stringable;

class Store implements SessionInterface
{
    protected string $id;
    protected string $name;
    protected array $attributes = [];
    protected SessionHandlerInterface $handler;
    protected bool $started = false;

    public function __construct(string $name, SessionHandlerInterface $handler, ?string $id = null)
    {
        $this->name = $name;
        $this->handler = $handler;
        $this->setId($id);
    }

    public function start(): bool
    {
        $this->loadSession();
        
        if (!$this->has('_token')) {
            $this->regenerateToken();
        }

        return $this->started = true;
    }

    protected function loadSession(): void
    {
        $this->attributes = array_merge($this->attributes, $this->readFromHandler());
    }

    protected function readFromHandler(): array
    {
        if ($data = $this->handler->read($this->getId())) {
            $data = @unserialize($data);
            if ($data !== false && is_array($data)) {
                return $data;
            }
        }
        return [];
    }

    public function save(): void
    {
        $this->ageFlashData();
        $this->handler->write($this->getId(), serialize($this->attributes));
        $this->started = false;
    }

    public function ageFlashData(): void
    {
        $forget = $this->get('_flash.old', []);
        
        foreach ($forget as $key) {
            $this->forget($key);
        }
        
        $this->put('_flash.old', $this->get('_flash.new', []));
        $this->put('_flash.new', []);
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function put(string|array $key, mixed $value = null): void
    {
        if (!is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            $this->attributes[$arrayKey] = $arrayValue;
        }
    }

    public function forget(string|array $keys): void
    {
        foreach ((array) $keys as $key) {
            unset($this->attributes[$key]);
        }
    }

    public function flush(): void
    {
        $this->attributes = [];
    }

    public function flash(string $key, mixed $value = true): void
    {
        $this->put($key, $value);
        $this->push('_flash.new', $key);
        $this->removeFromOldFlashData([$key]);
    }

    public function now(string $key, mixed $value = true): void
    {
        $this->put($key, $value);
        $this->push('_flash.old', $key);
    }

    public function reflash(): void
    {
        $this->mergeNewFlashes($this->get('_flash.old', []));
        $this->put('_flash.old', []);
    }

    public function keep(mixed $keys = null): void
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $this->mergeNewFlashes($keys);
        $this->removeFromOldFlashData($keys);
    }

    protected function mergeNewFlashes(array $keys): void
    {
        $values = array_unique(array_merge($this->get('_flash.new', []), $keys));
        $this->put('_flash.new', $values);
    }

    protected function removeFromOldFlashData(array $keys): void
    {
        $this->put('_flash.old', array_diff($this->get('_flash.old', []), $keys));
    }
    
    protected function push(string $key, mixed $value): void
    {
        $array = $this->get($key, []);
        $array[] = $value;
        $this->put($key, $array);
    }

    public function regenerate(bool $destroy = false): bool
    {
        if ($destroy) {
            $this->handler->destroy($this->getId());
        }

        $this->setId($this->generateSessionId());
        
        return true;
    }
    
    public function invalidate(): bool
    {
        $this->flush();
        return $this->regenerate(true);
    }

    public function regenerateToken(): void
    {
        $this->put('_token', bin2hex(random_bytes(20)));
    }

    public function token(): ?string
    {
        return $this->get('_token');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $this->isValidId($id) ? $id : $this->generateSessionId();
    }

    protected function isValidId(?string $id): bool
    {
        return is_string($id) && preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $id);
    }

    protected function generateSessionId(): string
    {
        return bin2hex(random_bytes(20));
    }
}
