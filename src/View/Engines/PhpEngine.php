<?php

declare(strict_types=1);

namespace Palet\Framework\View\Engines;

use Palet\Framework\Contracts\View\EngineInterface;
use Throwable;

class PhpEngine implements EngineInterface
{
    public function get(string $path, array $data = []): string
    {
        return $this->evaluatePath($path, $data);
    }

    protected function evaluatePath(string $__path, array $__data): string
    {
        $obLevel = ob_get_level();

        ob_start();

        extract($__data, EXTR_SKIP);

        try {
            include $__path;
        } catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        $output = ltrim(ob_get_clean());
        
        // Auto-inject CSRF token into forms with method POST, PUT, DELETE, PATCH
        if (str_contains(strtoupper($output), '<FORM') && class_exists('\\Palet\\Framework\\Foundation\\Application')) {
            $app = \Palet\Framework\Foundation\Application::getInstance();
            if ($app !== null && $app->has(\Palet\Framework\Contracts\Session\SessionInterface::class)) {
                $session = $app->make(\Palet\Framework\Contracts\Session\SessionInterface::class);
                $token = method_exists($session, 'token') ? $session->token() : $session->get('_token');
                
                if ($token) {
                    $csrfField = '<input type="hidden" name="_token" value="' . htmlspecialchars((string) $token) . '">';
                    // Regex to find form tags that DO NOT have method="GET" (case-insensitive)
                    // It will match any <form ...>
                    $output = preg_replace_callback('/<form\s+[^>]*>/i', function($matches) use ($csrfField) {
                        $formTag = $matches[0];
                        // If it's a GET form, we don't need CSRF token
                        if (preg_match('/method\s*=\s*[\'"]?GET[\'"]?/i', $formTag)) {
                            return $formTag;
                        }
                        return $formTag . "\n    " . $csrfField;
                    }, $output);
                }
            }
        }

        return $output;
    }

    protected function handleViewException(Throwable $e, int $obLevel): void
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }
}
