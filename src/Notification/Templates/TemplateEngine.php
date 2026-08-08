<?php

declare(strict_types=1);

namespace Palet\Framework\Notification\Templates;

use Palet\Framework\Contracts\Notification\TemplateInterface;

class TemplateEngine implements TemplateInterface
{
    protected array $templates = [];

    public function registerTemplate(string $name, string $content): void
    {
        $this->templates[$name] = $content;
    }

    public function render(string $templateName, array $data): string
    {
        $template = $this->templates[$templateName] ?? '';

        foreach ($data as $key => $value) {
            $template = str_replace('{{ ' . $key . ' }}', (string)$value, $template);
            $template = str_replace('{{' . $key . '}}', (string)$value, $template);
        }

        return $template;
    }
}
