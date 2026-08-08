<?php

declare(strict_types=1);

namespace Palet\Framework\View\Compiler\Component;

class ComponentTagCompiler
{
    /**
     * Compile the component tags.
     */
    public function compile(string $value): string
    {
        $value = $this->compileSlots($value);
        $value = $this->compileTags($value);
        $value = $this->compileClosingTags($value);

        return $value;
    }

    protected function compileSlots(string $value): string
    {
        $value = preg_replace_callback(
            '/<x-slot(?:\s+name=[\'"]([a-zA-Z0-9_-]+)[\'"])?\s*>/',
            function (array $matches) {
                $name = $matches[1] ?? 'default';
                return "<?php \$__env->slot('{$name}'); ?>";
            },
            $value
        );

        return preg_replace('/<\/x-slot\s*>/', '<?php $__env->endSlot(); ?>', $value);
    }

    protected function compileTags(string $value): string
    {
        return preg_replace_callback(
            '/<x-([a-zA-Z0-9_-]+)(\s+[^>]*)?\/?>/',
            function (array $matches) {
                $component = $matches[1];
                $attributes = $matches[2] ?? '';
                $isSelfClosing = str_ends_with($matches[0], '/>');

                $parsedAttributes = $this->parseAttributes($attributes);
                $attributesString = $this->attributesToString($parsedAttributes);

                $compiled = "<?php \$__env->startComponent('{$component}', {$attributesString}); ?>";

                if ($isSelfClosing) {
                    $compiled .= "\n<?php echo \$__env->renderComponent(); ?>";
                }

                return $compiled;
            },
            $value
        );
    }

    protected function compileClosingTags(string $value): string
    {
        return preg_replace(
            '/<\/x-([a-zA-Z0-9_-]+)\s*>/',
            '<?php echo $__env->renderComponent(); ?>',
            $value
        );
    }

    protected function parseAttributes(string $attributes): array
    {
        if (trim($attributes) === '') {
            return [];
        }

        $parsed = [];
        preg_match_all('/(:?[a-zA-Z0-9_-]+(?:-[a-zA-Z0-9_-]+)*)(?:="([^"]*)")?/', $attributes, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $name = $match[1];
            $value = $match[2] ?? 'true';
            $parsed[$name] = $value;
        }

        return $parsed;
    }

    protected function attributesToString(array $attributes): string
    {
        if (empty($attributes)) {
            return '[]';
        }

        $string = '[';
        foreach ($attributes as $key => $value) {
            if (str_starts_with($key, ':')) {
                $key = substr($key, 1);
                $string .= "'{$key}' => {$value}, ";
            } elseif ($value !== 'true') {
                $string .= "'{$key}' => '{$value}', ";
            } else {
                $string .= "'{$key}' => true, ";
            }
        }
        $string .= ']';

        return $string;
    }
}
