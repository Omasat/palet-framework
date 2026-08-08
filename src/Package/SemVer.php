<?php

declare(strict_types=1);

namespace Palet\Framework\Package;

class SemVer
{
    /**
     * Compare two semantic versions.
     * 1 = v1 > v2, 0 = equal, -1 = v1 < v2
     */
    public function compare(string $v1, string $v2): int
    {
        $v1 = $this->normalize($v1);
        $v2 = $this->normalize($v2);
        
        return version_compare($v1, $v2);
    }

    /**
     * Check if a version satisfies a constraint (e.g. ^1.2.0, ~2.1)
     */
    public function satisfies(string $version, string $constraint): bool
    {
        if ($constraint === '*') {
            return true;
        }

        $version = $this->normalize($version);
        
        if (str_starts_with($constraint, '^')) {
            return $this->satisfiesCaret($version, substr($constraint, 1));
        }

        if (str_starts_with($constraint, '~')) {
            return $this->satisfiesTilde($version, substr($constraint, 1));
        }
        
        // Exact match fallback
        return $this->compare($version, $constraint) === 0;
    }

    protected function satisfiesCaret(string $version, string $constraint): bool
    {
        $constraint = $this->normalize($constraint);
        
        $parts = explode('.', $constraint);
        $major = $parts[0] ?? '0';
        
        $nextMajor = (int)$major + 1 . '.0.0';
        
        return $this->compare($version, $constraint) >= 0 && $this->compare($version, $nextMajor) < 0;
    }

    protected function satisfiesTilde(string $version, string $constraint): bool
    {
        $constraint = $this->normalize($constraint);
        
        $parts = explode('.', $constraint);
        $major = $parts[0] ?? '0';
        $minor = $parts[1] ?? '0';
        
        $nextMinor = $major . '.' . ((int)$minor + 1) . '.0';
        
        return $this->compare($version, $constraint) >= 0 && $this->compare($version, $nextMinor) < 0;
    }

    protected function normalize(string $version): string
    {
        // Remove v prefix
        $version = ltrim($version, 'v');
        
        // Pad with .0 if needed
        $parts = explode('.', $version);
        while (count($parts) < 3) {
            $parts[] = '0';
        }
        
        return implode('.', array_slice($parts, 0, 3));
    }
}
