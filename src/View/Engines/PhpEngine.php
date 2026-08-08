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

        return ltrim(ob_get_clean());
    }

    protected function handleViewException(Throwable $e, int $obLevel): void
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }
}
