<?php

declare(strict_types=1);

namespace Palet\Framework\Generator;

use Palet\Framework\Contracts\Generator\TemplateEngineInterface;
use Palet\Framework\Contracts\Generator\PlaceholderResolverInterface;

class TemplateEngine implements TemplateEngineInterface
{
    protected PlaceholderResolverInterface $resolver;

    public function __construct(PlaceholderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function compile(string $templateContent, array $variables): string
    {
        // Simple Placeholder replacement
        $compiled = $this->resolver->resolve($templateContent, $variables);

        // Simple If block support (e.g., @if(HasNamespace) ... @endif)
        // This is a basic implementation for scaffolding needs
        $compiled = preg_replace_callback('/@if\s*\(\s*([a-zA-Z0-9_]+)\s*\)(.*?)@endif/s', function ($matches) use ($variables) {
            $condition = $matches[1];
            $content = $matches[2];
            
            return !empty($variables[$condition]) ? $content : '';
        }, $compiled);

        return $compiled;
    }
}
