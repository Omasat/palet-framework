<?php

declare(strict_types=1);

namespace Palet\Framework\Generator;

use Palet\Framework\Contracts\Generator\PlaceholderResolverInterface;

class PlaceholderResolver implements PlaceholderResolverInterface
{
    public function resolve(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $content = str_replace('{{ ' . $key . ' }}', (string) $value, $content);
                // Also handle without spaces
                $content = str_replace('{{' . $key . '}}', (string) $value, $content);
            }
        }

        return $content;
    }
}
