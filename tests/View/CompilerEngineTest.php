<?php

declare(strict_types=1);

namespace Tests\View;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Factory;
use Palet\Framework\View\FileViewFinder;
use Palet\Framework\View\Engines\CompilerEngine;
use Palet\Framework\View\Compiler\TemplateCompiler;

class CompilerEngineTest extends TestCase
{
    protected string $fixturesPath;
    protected Factory $factory;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures';
        $finder = new FileViewFinder([$this->fixturesPath]);
        $this->factory = new Factory($finder);
        
        $this->factory->addEngine('palet.php', new CompilerEngine(new TemplateCompiler($this->fixturesPath . '/cache')));
        
        file_put_contents($this->fixturesPath . '/hello.palet.php', 'Hello {{ $name }}');
        
        // Nested layout
        file_put_contents($this->fixturesPath . '/layout.palet.php', 'Header-@yield("content")-Footer');
        file_put_contents($this->fixturesPath . '/child.palet.php', "@extends('layout')\n@section('content')\nChildContent\n@endsection");
    }

    protected function tearDown(): void
    {
        unlink($this->fixturesPath . '/hello.palet.php');
        unlink($this->fixturesPath . '/layout.palet.php');
        unlink($this->fixturesPath . '/child.palet.php');
        
        $files = glob($this->fixturesPath . '/cache/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function test_compiles_and_renders_template()
    {
        $view = $this->factory->make('hello', ['name' => 'Palet']);
        $this->assertEquals('Hello Palet', $view->render());
        
        // Ensure htmlspecialchars is applied
        $view = $this->factory->make('hello', ['name' => '<b>Bold</b>']);
        $this->assertEquals('Hello &lt;b&gt;Bold&lt;/b&gt;', $view->render());
    }

    public function test_layout_rendering()
    {
        $view = $this->factory->make('child');
        
        $rendered = str_replace(["\n", "\r"], '', $view->render());
        $expected = "Header-ChildContent-Footer";
        $this->assertEquals($expected, $rendered);
    }
}
