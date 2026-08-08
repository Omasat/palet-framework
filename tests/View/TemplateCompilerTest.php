<?php

declare(strict_types=1);

namespace Tests\View;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Compiler\TemplateCompiler;

class TemplateCompilerTest extends TestCase
{
    protected string $cachePath;
    protected TemplateCompiler $compiler;

    protected function setUp(): void
    {
        $this->cachePath = __DIR__ . '/fixtures/cache';
        $this->compiler = new TemplateCompiler($this->cachePath);
    }

    public function test_compiles_escaped_echos()
    {
        $template = 'Hello, {{ $name }}!';
        $reflection = new \ReflectionClass($this->compiler);
        $method = $reflection->getMethod('compileString');
        
        $compiled = $method->invoke($this->compiler, $template);
        
        $this->assertEquals('Hello, <?php echo \Palet\Framework\View\Html\HtmlEscaper::escape($name); ?>!', $compiled);
    }

    public function test_compiles_raw_echos()
    {
        $template = 'Hello, {!! $name !!}!';
        $reflection = new \ReflectionClass($this->compiler);
        $method = $reflection->getMethod('compileString');
        
        $compiled = $method->invoke($this->compiler, $template);
        
        $this->assertEquals('Hello, <?php echo $name; ?>!', $compiled);
    }

    public function test_compiles_if_statements()
    {
        $template = '@if($user->isAdmin()) Admin @elseif($user->isMod()) Mod @else Guest @endif';
        $reflection = new \ReflectionClass($this->compiler);
        $method = $reflection->getMethod('compileString');
        
        $compiled = $method->invoke($this->compiler, $template);
        
        $expected = '<?php if($user->isAdmin()): ?> Admin <?php elseif($user->isMod()): ?> Mod <?php else: ?> Guest <?php endif; ?>';
        $this->assertEquals($expected, $compiled);
    }

    public function test_compiles_foreach()
    {
        $template = '@foreach($users as $user) {{ $user }} @endforeach';
        $reflection = new \ReflectionClass($this->compiler);
        $method = $reflection->getMethod('compileString');
        
        $compiled = $method->invoke($this->compiler, $template);
        
        $expected = '<?php foreach($users as $user): ?> <?php echo \Palet\Framework\View\Html\HtmlEscaper::escape($user); ?> <?php endforeach; ?>';
        $this->assertEquals($expected, $compiled);
    }

    public function test_compiles_layout_directives()
    {
        // compileString is protected, so we can't test footer directly without mocking.
        // Instead, we will test compile() which processes footer.
        $template = "@extends('layout')\n@section('content')\nContent\n@endsection";
        $file = $this->cachePath . '/test_layout.palet.php';
        file_put_contents($file, $template);
        
        $this->compiler->compile($file);
        
        $compiledFile = $this->compiler->getCompiledPath($file);
        $compiled = file_get_contents($compiledFile);
        
        $expected = "\n<?php \$__env->startSection('content'); ?>\nContent\n<?php \$__env->stopSection(); ?>\n<?php echo \$__env->make('layout')->render(); ?>";
        $this->assertEquals($expected, $compiled);
        
        unlink($file);
        unlink($compiledFile);
    }
}
