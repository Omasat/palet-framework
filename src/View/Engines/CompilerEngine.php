<?php

declare(strict_types=1);

namespace Palet\Framework\View\Engines;

use Palet\Framework\Contracts\View\ViewCompilerInterface;

class CompilerEngine extends PhpEngine
{
    protected ViewCompilerInterface $compiler;

    public function __construct(ViewCompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    public function get(string $path, array $data = []): string
    {
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        $compiled = $this->compiler->getCompiledPath($path);

        return $this->evaluatePath($compiled, $data);
    }
}
