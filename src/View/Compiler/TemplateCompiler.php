<?php

declare(strict_types=1);

namespace Palet\Framework\View\Compiler;

use Palet\Framework\Contracts\View\ViewCompilerInterface;
use RuntimeException;

class TemplateCompiler implements ViewCompilerInterface
{
    protected string $cachePath;

    public function __construct(string $cachePath)
    {
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0777, true);
        }
        $this->cachePath = rtrim($cachePath, '/');
    }

    public function getCompiledPath(string $path): string
    {
        return $this->cachePath . '/' . sha1($path) . '.php';
    }

    public function isExpired(string $path): bool
    {
        $compiled = $this->getCompiledPath($path);

        if (!file_exists($compiled)) {
            return true;
        }

        return filemtime($path) >= filemtime($compiled);
    }

    protected array $footer = [];

    public function compile(string $path): void
    {
        if (!file_exists($path)) {
            throw new RuntimeException("View file not found: {$path}");
        }

        $this->footer = [];
        
        $contents = file_get_contents($path);

        $contents = $this->compileString($contents);

        if (!empty($this->footer)) {
            $contents .= "\n" . implode("\n", $this->footer);
        }

        file_put_contents($this->getCompiledPath($path), $contents);
    }

    protected function compileString(string $value): string
    {
        $value = (new Component\ComponentTagCompiler())->compile($value);
        $value = $this->compileRawEchos($value);
        $value = $this->compileEscapedEchos($value);
        $value = $this->compileStatements($value);
        
        return $value;
    }

    protected function compileRawEchos(string $value): string
    {
        return preg_replace('/\{\!\!\s*(.+?)\s*\!\!\}/s', '<?php echo $1; ?>', $value);
    }

    protected function compileEscapedEchos(string $value): string
    {
        return preg_replace('/\{\{\s*(.+?)\s*\}\}/s', '<?php echo \Palet\Framework\View\Html\HtmlEscaper::escape($1); ?>', $value);
    }

    protected function compileStatements(string $value): string
    {
        $value = $this->compileVite($value);
        return preg_replace_callback(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            function ($match) {
                return $this->compileStatement($match);
            },
            $value
        );
    }

    protected function compileVite(string $value): string
    {
        return preg_replace('/@vite\(\s*\[?(.*?)\]?\s*\)/', '<?php echo $__env->vite([$1]); ?>', $value);
    }

    protected function compileStatement(array $match): string
    {
        $directive = $match[1];
        $space = $match[2] ?? '';
        $arguments = $match[3] ?? '';

        if (method_exists($this, $method = 'compile' . ucfirst($directive))) {
            return $this->$method($arguments) . $space;
        }

        return $match[0];
    }

    protected function compileIf(string $expression): string
    {
        return "<?php if{$expression}: ?>";
    }

    protected function compileElseif(string $expression): string
    {
        return "<?php elseif{$expression}: ?>";
    }

    protected function compileElse(): string
    {
        return "<?php else: ?>";
    }

    protected function compileEndif(): string
    {
        return "<?php endif; ?>";
    }

    protected function compileForeach(string $expression): string
    {
        return "<?php foreach{$expression}: ?>";
    }

    protected function compileEndforeach(): string
    {
        return "<?php endforeach; ?>";
    }

    // Layout Directives
    protected function compileExtends(string $expression): string
    {
        $this->footer[] = "<?php echo \$__env->make{$expression}->render(); ?>";
        return '';
    }

    protected function compileSection(string $expression): string
    {
        return "<?php \$__env->startSection{$expression}; ?>";
    }

    protected function compileEndsection(): string
    {
        return "<?php \$__env->stopSection(); ?>";
    }

    protected function compileYield(string $expression): string
    {
        return "<?php echo \$__env->yieldContent{$expression}; ?>";
    }

    protected function compileInclude(string $expression): string
    {
        return "<?php echo \$__env->make{$expression}->render(); ?>";
    }
}
