<?php

declare(strict_types=1);

namespace Palet\Framework\Translation;

use Palet\Framework\Contracts\Translation\TranslatorInterface;
use Palet\Framework\Contracts\Translation\TranslationLoaderInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Translation\Events\LocaleChanged;
use Palet\Framework\Translation\Events\TranslationMissing;

class Translator implements TranslatorInterface
{
    protected TranslationLoaderInterface $loader;
    protected string $locale;
    protected string $fallback;
    protected ?EventDispatcherInterface $events = null;
    protected MessageFormatter $formatter;
    
    // Memory cache for loaded translations
    protected array $loaded = [];

    public function __construct(TranslationLoaderInterface $loader, string $locale, string $fallback = 'en')
    {
        $this->loader = $loader;
        $this->locale = $locale;
        $this->fallback = $fallback;
        $this->formatter = new MessageFormatter();
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function get(string $key, array $replace = [], ?string $locale = null): string|array|null
    {
        $locale = $locale ?? $this->locale;
        $fallback = $this->fallback;

        $line = $this->getLine($key, $locale);

        if ($line === null && $locale !== $fallback) {
            $line = $this->getLine($key, $fallback);
        }

        if ($line === null) {
            if ($this->events) {
                $this->events->dispatch(new TranslationMissing($key, $locale));
            }
            return $key;
        }

        if (is_array($line)) {
            return $line;
        }

        return $this->formatter->format($line, $replace, $locale);
    }

    public function choice(string $key, int|float|array|\Countable $number, array $replace = [], ?string $locale = null): string
    {
        $line = $this->get($key, [], $locale);

        if (is_array($line)) {
            return $key;
        }
        
        if (is_array($number) || $number instanceof \Countable) {
            $number = count($number);
        }

        return $this->formatter->choice($line, $number, $replace, $locale);
    }

    public function has(string $key, ?string $locale = null, bool $fallback = true): bool
    {
        $locale = $locale ?? $this->locale;

        $line = $this->getLine($key, $locale);
        
        if ($line !== null) {
            return true;
        }

        if ($fallback && $locale !== $this->fallback) {
            return $this->getLine($key, $this->fallback) !== null;
        }

        return false;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;

        if ($this->events) {
            $this->events->dispatch(new LocaleChanged($locale));
        }
    }

    public function getFallback(): string
    {
        return $this->fallback;
    }

    public function setFallback(string $fallback): void
    {
        $this->fallback = $fallback;
    }

    protected function getLine(string $key, string $locale): mixed
    {
        [$namespace, $group, $item] = $this->parseKey($key);

        $this->load($namespace, $group, $locale);

        if ($item === null) {
            return $this->loaded[$namespace][$group][$locale] ?? null;
        }

        $array = $this->loaded[$namespace][$group][$locale] ?? [];

        // Simple dot notation resolution
        $keys = explode('.', $item);
        foreach ($keys as $k) {
            if (!is_array($array) || !array_key_exists($k, $array)) {
                return null;
            }
            $array = $array[$k];
        }

        return $array;
    }

    protected function load(string $namespace, string $group, string $locale): void
    {
        if ($this->isLoaded($namespace, $group, $locale)) {
            return;
        }

        $lines = $this->loader->load($locale, $group, $namespace === '*' ? null : $namespace);

        $this->loaded[$namespace][$group][$locale] = $lines;
    }

    protected function isLoaded(string $namespace, string $group, string $locale): bool
    {
        return isset($this->loaded[$namespace][$group][$locale]);
    }

    protected function parseKey(string $key): array
    {
        // namespace::group.item
        $namespace = '*';
        $group = '*';
        $item = null;

        if (str_contains($key, '::')) {
            $parts = explode('::', $key, 2);
            $namespace = $parts[0];
            $key = $parts[1];
        }

        if (str_contains($key, '.')) {
            $parts = explode('.', $key, 2);
            $group = $parts[0];
            $item = $parts[1];
        } else {
            // It might be a JSON key if we don't use dots, or just a group.
            // In standard systems, if it has no dot, it's a JSON key.
            // Here we map JSON keys to group '*' and item to the whole key.
            $group = '*';
            $item = $key;
        }

        return [$namespace, $group, $item];
    }
}
