<?php

declare(strict_types=1);

namespace Palet\Framework\Routing;

class RouteGroup
{
    /**
     * Merge route group attributes.
     */
    public static function merge(array $new, array $old): array
    {
        if (isset($new['domain'])) {
            unset($old['domain']);
        }

        $merged = array_merge($old, $new);

        if (isset($old['prefix'], $new['prefix'])) {
            $merged['prefix'] = static::formatPrefix($old['prefix'], $new['prefix']);
        }

        if (isset($old['name'], $new['name'])) {
            $merged['name'] = trim($old['name'], '.') . '.' . trim($new['name'], '.');
        }

        if (isset($old['where'], $new['where'])) {
            $merged['where'] = array_merge($old['where'], $new['where']);
        }

        if (isset($old['middleware'], $new['middleware'])) {
            $merged['middleware'] = array_merge((array) $old['middleware'], (array) $new['middleware']);
        }

        return $merged;
    }

    /**
     * Format the prefix for the new group.
     */
    protected static function formatPrefix(string $old, string $new): string
    {
        $old = trim($old, '/');
        $new = trim($new, '/');
        
        if ($old === '') {
            return $new;
        }

        if ($new === '') {
            return $old;
        }

        return $old . '/' . $new;
    }
}
